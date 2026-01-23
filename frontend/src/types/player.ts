export type TeamType = "first_team" | "youth" | "women"

export type PlayerPosition =
    | "GK"
    | "CB" | "LB" | "RB"
    | "DM" | "CM" | "AM" | "LM" | "RM"
    | "FC"

export type Player = {
    id: number
    name: string
    team_type: TeamType
    birth_year: number
    shirt_number: number
    position: PlayerPosition
    photo: string | null
    is_active: boolean
    created_at?: string
    updated_at?: string
}
