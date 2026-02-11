export type StaffRole =
    | "head_coach"
    | "assistant_coach"
    | "goalkeeper_coach"
    | "fitness_coach"
    | "physio"
    | "analyst"
    | "team_manager"

export type StaffMember = {
    id: number | string
    team_type: string
    name: string
    role: StaffRole
    photo?: string | null
    photo_url?: string | null
    photo_thumb_url?: string | null
    sort_order?: number | null
    is_active: boolean
}
