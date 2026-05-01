import type { ClubDocumentsResponse } from '@/types/clubDocument'

export async function getClubDocuments(): Promise<ClubDocumentsResponse> {
    const response = await fetch('/api/club-documents', {
        headers: {
            Accept: 'application/json',
        },
    })

    if (!response.ok) {
        throw new Error('Greška prilikom učitavanja dokumenata.')
    }

    return response.json()
}