export interface ClubDocument {
    id: number
    title: string
    uploaded_at: string | null
    file_url: string | null
    file_extension: string | null
    file_size: string | null
}

export interface ClubDocumentsResponse {
    data: ClubDocument[]
}