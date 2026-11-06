import type { LeaderboardRow } from './api/leaderboard.ts'

type LeaderboardTableProps = {
  rows: LeaderboardRow[]
}

export function LeaderboardTable({ rows }: LeaderboardTableProps) {
  return (
    <div className="mt-8 overflow-x-auto">
      <table className="w-full border-collapse text-left">
        <thead>
          <tr className="border-b border-slate-700 text-sm text-slate-400">
            <th scope="col" className="px-3 py-2 font-medium">
              Rank
            </th>
            <th scope="col" className="px-3 py-2 font-medium">
              Id
            </th>
            <th scope="col" className="px-3 py-2 font-medium">
              Name
            </th>
            <th scope="col" className="px-3 py-2 text-right font-medium">
              Commission
            </th>
          </tr>
        </thead>
        <tbody>
          {rows.length === 0 ? (
            <tr>
              <td colSpan={4} className="px-3 py-6 text-center text-slate-400">
                No eligible affiliates.
              </td>
            </tr>
          ) : (
            rows.map((row) => (
              <tr key={row.id} className="border-b border-slate-800">
                <td className="px-3 py-3">{row.rank}</td>
                <td className="px-3 py-3">{row.id}</td>
                <td className="px-3 py-3">{row.name}</td>
                <td className="px-3 py-3 text-right tabular-nums">
                  {formatCommission(row.commission)}
                </td>
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  )
}

function formatCommission(commission: number): string {
  return commission.toLocaleString('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  })
}
