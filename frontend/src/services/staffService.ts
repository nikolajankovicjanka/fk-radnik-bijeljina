import type {StaffMember} from "@/types/staff"

type ApiResponse<T> = { data: T }

function buildUrl(teamType?: string, active: 0 | 1 = 1) {
    const url = new URL("/api/staff", window.location.origin)
    if (teamType) url.searchParams.set("team_type", teamType)
    url.searchParams.set("active", String(active))
    return url.toString()
}

export async function fetchStaff(teamType?: string): Promise<StaffMember[]> {
    const res = await fetch(buildUrl(teamType, 1), {
        headers: {Accept: "application/json"},
    })
    if (!res.ok) {
        throw new Error(`Failed to load staff (${res.status})`)
    }
    const json = (await res.json()) as ApiResponse<StaffMember[]>
    return json.data ?? []
}
