<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { FileText, Loader2 } from 'lucide-vue-next'
import { getClubDocuments } from '@/services/clubDocumentsService'
import type { ClubDocument } from '@/types/clubDocument'

const documents = ref<ClubDocument[]>([])
const isLoading = ref(true)
const error = ref<string | null>(null)

const visibleDocuments = computed(() => {
  return (documents.value ?? []).filter((document) => Boolean(document.file_url))
})

const hasDocuments = computed(() => visibleDocuments.value.length > 0)

const loadDocuments = async () => {
  isLoading.value = true
  error.value = null

  try {
    const response = await getClubDocuments()

    // Backend već vraća samo published=yes dokumente.
    documents.value = (response.data ?? []).filter((document) => Boolean(document.file_url))
  } catch (err) {
    console.error(err)
    error.value = 'Dokumenti trenutno nisu dostupni.'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadDocuments()
})
</script>

<template>
  <section class="club-documents-section">
    <div class="club-documents-container">
      <div class="club-documents-header">
        <h2 class="club-documents-title">Dokumenti kluba</h2>
      </div>

      <div v-if="isLoading" class="club-documents-state">
        <Loader2 class="club-documents-loader" />
        <span>Učitavanje dokumenata...</span>
      </div>

      <div v-else-if="error" class="club-documents-state club-documents-state--error">
        {{ error }}
      </div>

      <div v-else-if="!hasDocuments" class="club-documents-state">
        Trenutno nema objavljenih dokumenata.
      </div>

      <div v-else class="club-documents-grid">
        <a
            v-for="document in visibleDocuments"
            :key="document.id"
            :href="document.file_url || '#'"
            target="_blank"
            rel="noopener noreferrer"
            class="club-documents-card"
        >
          <div class="club-documents-icon">
            <FileText />
          </div>

          <span class="club-documents-name">
            {{ document.title }}
          </span>
        </a>
      </div>
    </div>
  </section>
</template>

<style scoped>
.club-documents-section {
  width: 100%;
  background: #ffffff;
  padding: 36px 0;
}

.club-documents-container {
  width: 100%;
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 16px;
}

.club-documents-header {
  margin-bottom: 22px;
}

.club-documents-title {
  margin: 0;
  font-size: 28px;
  line-height: 1.2;
  font-weight: 800;
  color: #0a2d6b;
}

.club-documents-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.club-documents-card {
  min-height: 150px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  text-align: center;
  text-decoration: none;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 18px;
  padding: 22px 16px;
  transition:
      transform 0.2s ease,
      border-color 0.2s ease,
      box-shadow 0.2s ease,
      background-color 0.2s ease;
}

.club-documents-card:hover {
  transform: translateY(-2px);
  border-color: #d1d5db;
  box-shadow: 0 10px 20px rgb(27 93 219);
  background-color: #fafafa;
}

.club-documents-icon {
  width: 54px;
  height: 54px;
  min-width: 54px;
  border-radius: 16px;
  background: #fee2e2;
  color: #1b5ddb;
  display: flex;
  align-items: center;
  justify-content: center;
}

.club-documents-icon :deep(svg) {
  width: 28px;
  height: 28px;
}

.club-documents-name {
  width: 100%;
  font-size: 14px;
  line-height: 1.45;
  font-weight: 700;
  color: #111827;
  word-break: break-word;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.club-documents-state {
  display: flex;
  align-items: center;
  gap: 10px;
  min-height: 80px;
  padding: 18px;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  background: #ffffff;
  color: #4b5563;
  font-size: 14px;
}

.club-documents-state--error {
  color: #b91c1c;
  border-color: #fecaca;
  background: #fef2f2;
}

.club-documents-loader {
  width: 18px;
  height: 18px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 1024px) {
  .club-documents-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .club-documents-section {
    padding: 28px 0;
  }

  .club-documents-title {
    font-size: 24px;
  }

  .club-documents-grid {
    grid-template-columns: 1fr;
    gap: 12px;
  }

  .club-documents-card {
    min-height: 130px;
    padding: 18px 14px;
  }

  .club-documents-name {
    font-size: 14px;
  }
}
</style>