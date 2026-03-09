export type StaffTeamType =
    | "first_team"
    | "youth"
    | "women"
    | "board"

export type StaffRole =
    | "head_coach"
    | "assistant_coach"
    | "gk_coach"
    | "fitness_coach"
    | "physio"
    | "doctor"
    | "analyst"
    | "team_manager"
    | "president"
    | "general_director"
    | "sport_director"
    | "board_secretary"
    | "club_secretary"
    | "board_member"
    | "youth_director"
    | "economat"

export type StaffMember = {
    id: number | string
    team_type: StaffTeamType
    name: string
    role: StaffRole
    photo?: string | null
    photo_url?: string | null
    photo_thumb_url?: string | null
    sort_order?: number | null
    is_active: boolean
}