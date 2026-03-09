import type {StaffMember} from "@/types/staff"

function buildUrl(teamType?: string, active: 0 | 1 = 1) {
    const url = new URL("/api/staff", window.location.origin)

    if (teamType) {
        url.searchParams.set("team_type", teamType)
    }

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

    const json = await res.json()

    // Podržava:
    // 1) Raw array: [...]
    // 2) Laravel: { data: [...] }
    // 3) Bilo koji drugi shape -> vraća []
    const data = Array.isArray(json)
        ? json
        : Array.isArray(json?.data)
            ? json.data
            : []

    return data as StaffMember[]
}
