export type TeamType = 'first_team' | 'youth' | 'women'
export type GameStatus = 'scheduled' | 'live' | 'finished'

export type Club = {
  id: number
  name: string
  slug: string
  logo: string | null // "clubs/xxx.png"
}

export type Game = {
  id: number
  team_type: TeamType
  home_club_id: number
  away_club_id: number
  home_score: number | null
  away_score: number | null
  kickoff_at: string // ISO
  status: GameStatus
  stadium: string | null
  round: string | null
  home_club: Club
  away_club: Club
}
