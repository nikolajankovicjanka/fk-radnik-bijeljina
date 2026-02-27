export type YouthGroup = "u19" | "u17" | "u15"

const RULES: Record<YouthGroup, { min: number; max?: number }> = {
    u19: {min: 2007, max: 2008},
    u17: {min: 2009, max: 2010},
    u15: {min: 2011}, // 2011+
}

export function inYouthGroup(birth_year: number, group: YouthGroup) {
    const r = RULES[group]
    return r.max != null ? birth_year >= r.min && birth_year <= r.max : birth_year >= r.min
}