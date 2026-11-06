export type LeaderboardRow = {
  rank: number
  id: number
  name: string
  commission: number
}

export class LeaderboardRequestError extends Error {
  constructor(message = 'Could not load the leaderboard.') {
    super(message)
    this.name = 'LeaderboardRequestError'
  }
}

export async function fetchLeaderboard(
  signal?: AbortSignal,
): Promise<LeaderboardRow[]> {
  let response: Response

  try {
    response = await fetch('/api/leaderboard', { signal })
  } catch (error) {
    if (isAbortError(error)) {
      throw error
    }

    throw new LeaderboardRequestError()
  }

  if (!response.ok) {
    throw new LeaderboardRequestError()
  }

  let body: unknown

  try {
    body = await response.json()
  } catch {
    throw new LeaderboardRequestError()
  }

  return parseLeaderboard(body)
}

function parseLeaderboard(body: unknown): LeaderboardRow[] {
  if (!isRecord(body) || !Array.isArray(body.data)) {
    throw new LeaderboardRequestError()
  }

  return body.data.map((row) => {
    if (
      !isRecord(row) ||
      typeof row.rank !== 'number' ||
      typeof row.id !== 'number' ||
      typeof row.name !== 'string' ||
      typeof row.commission !== 'number'
    ) {
      throw new LeaderboardRequestError()
    }

    return {
      rank: row.rank,
      id: row.id,
      name: row.name,
      commission: row.commission,
    }
  })
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null
}

function isAbortError(error: unknown): boolean {
  return error instanceof DOMException && error.name === 'AbortError'
}
