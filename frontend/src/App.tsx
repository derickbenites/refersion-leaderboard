import { useEffect, useState } from 'react'
import {
  fetchLeaderboard,
  type LeaderboardRow,
} from './api/leaderboard.ts'
import { LeaderboardTable } from './LeaderboardTable.tsx'

type Status =
  | { state: 'loading' }
  | { state: 'ready'; rows: LeaderboardRow[] }
  | { state: 'error' }

export default function App() {
  const [status, setStatus] = useState<Status>({ state: 'loading' })
  const [attempt, setAttempt] = useState(0)

  useEffect(() => {
    const controller = new AbortController()

    fetchLeaderboard(controller.signal)
      .then((rows) => {
        if (!controller.signal.aborted) {
          setStatus({ state: 'ready', rows })
        }
      })
      .catch((error: unknown) => {
        if (controller.signal.aborted) {
          return
        }

        if (error instanceof DOMException && error.name === 'AbortError') {
          return
        }

        setStatus({ state: 'error' })
      })

    return () => controller.abort()
  }, [attempt])

  return (
    <main className="min-h-svh bg-slate-950 px-6 py-16 text-slate-100">
      <div className="mx-auto max-w-3xl">
        <p className="text-sm font-medium tracking-wide text-slate-400">
          Refersion
        </p>
        <h1 className="mt-2 text-3xl font-semibold">Affiliate leaderboard</h1>

        {status.state === 'loading' ? (
          <p className="mt-8 text-slate-300" role="status">
            Loading leaderboard…
          </p>
        ) : null}

        {status.state === 'error' ? (
          <div className="mt-8" role="alert">
            <p className="text-slate-300">Could not load the leaderboard.</p>
            <button
              type="button"
              className="mt-4 rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-950"
              onClick={() => {
                setStatus({ state: 'loading' })
                setAttempt((current) => current + 1)
              }}
            >
              Try again
            </button>
          </div>
        ) : null}

        {status.state === 'ready' ? <LeaderboardTable rows={status.rows} /> : null}
      </div>
    </main>
  )
}
