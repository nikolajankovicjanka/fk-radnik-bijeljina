<template>
    <section class="legends">
        <div class="container">
            <header class="legends__header">
                <h2 class="legends__title">Legende FK Radnik</h2>
                <p class="legends__subtitle">
                    Ljudi i generacije koje su gradile identitet kluba.
                </p>
            </header>

            <div class="legends__grid">
                <article
                        v-for="card in cards"
                        :key="card.id"
                        class="legend-card"
                >
                    <div class="legend-card__top">
                        <span class="legend-card__tag">{{ card.tag }}</span>
                        <h3 class="legend-card__title">{{ card.title }}</h3>
                        <p class="legend-card__lead">{{ card.lead }}</p>
                    </div>

                    <div class="legend-card__body">
                        <p class="legend-card__text">
                            {{ card.text }}
                        </p>
                    </div>

                    <div class="legend-card__footer" v-if="enableModal">
                        <button class="legend-card__btn" @click="open(card)">
                            Pročitaj više
                        </button>
                    </div>
                </article>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="enableModal && active" class="modal" @click.self="close">
            <div class="modal__panel">
                <div class="modal__head">
                    <div>
                        <span class="modal__tag">{{ active.tag }}</span>
                        <h3 class="modal__title">{{ active.title }}</h3>
                        <p class="modal__lead">{{ active.lead }}</p>
                    </div>
                    <button class="modal__close" @click="close"
                            aria-label="Close">×
                    </button>
                </div>

                <div class="modal__content">
                    <p>{{ active.text }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import {ref} from "vue"

type LegendCard = {
    id: string
    tag: string
    title: string
    lead: string
    text: string
}

const enableModal = true // ako ne želiš modal -> false

const cards: LegendCard[] = [
    {
        id: "1916-1945",
        tag: "Pioniri",
        title: "Rane generacije Radnika (1916–1945)",
        lead: "Pioniri fudbala u Bijeljini",
        text:
            "Ova generacija postavila je temelje FK Radnik u vremenu kada se fudbal igrao iz čiste ljubavi prema igri. U skromnim uslovima, bez infrastrukture i opreme, stvorili su klub koji je postao centar sportskog života Bijeljine. Njihova posvećenost i entuzijazam oblikovali su duh Radnika koji traje i danas.",
    },
    {
        id: "1950-1970",
        tag: "Zlatne godine",
        title: "Zlatne generacije poslijeratnog perioda (1950–1970)",
        lead: "Vrijeme stabilnosti i prepoznatljivosti",
        text:
            "Pedesete i šezdesete godine donijele su snažan razvoj kluba. FK Radnik se takmičio u regionalnim i podsaveznim ligama, a klub je imao jasnu strukturu, prepoznatljiv stil igre i respekt širom regiona. Igrači i treneri iz ovog perioda ostavili su dubok trag i stvorili temelje modernog Radnika.",
    },
    {
        id: "iza-kulisa",
        tag: "Temelji",
        title: "Radnik i ljudi iza kulisa",
        lead: "Sportski radnici i organizatori",
        text:
            "Pored igrača na terenu, Radnik su gradili i brojni sportski radnici, funkcioneri i volonteri. Njihov doprinos u organizaciji, očuvanju kluba i radu sa mlađim kategorijama bio je ključan za opstanak i kontinuitet kluba kroz decenije.",
    },
    {
        id: "1960-1978",
        tag: "Kolektiv",
        title: "Generacije šezdesetih i sedamdesetih (1960–1978)",
        lead: "Snaga kolektiva i identitet kluba",
        text:
            "Sačuvane fotografije iz ovog perioda svjedoče o ekipama koje su igrale disciplinovano, borbeno i sa snažnim osjećajem pripadnosti. Ove generacije učvrstile su Radnik kao simbol Bijeljine i ostavile trajno nasljeđe budućim igračima.",
    },
    {
        id: "nasljedje",
        tag: "Danas",
        title: "Nasljeđe koje traje",
        lead: "Legende koje žive kroz klub",
        text:
            "Legende FK Radnik nisu samo imena iz prošlosti — one žive kroz današnji klub, mlade igrače i navijače. Njihova priča podsjeća da Radnik nije samo fudbalski klub, već dio identiteta grada i njegove istorije.",
    },
]

const active = ref<LegendCard | null>(null)

function open(card: LegendCard) {
    active.value = card
}

function close() {
    active.value = null
}
</script>

<style scoped>
.legends {
    padding: clamp(32px, 4vw, 56px) 0;
}

.container {
    width: min(1100px, calc(100% - 32px));
    margin: 0 auto;
}

.legends__header {
    margin-bottom: 18px;
}

.legends__title {
    font-size: clamp(22px, 2.6vw, 34px);
    font-weight: 900;
    letter-spacing: -0.02em;
    color: #0a2d6b;
    margin: 0 0 6px;
}

.legends__subtitle {
    margin: 0;
    color: rgba(0, 0, 0, 0.65);
    font-size: 14px;
}

.legends__grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 14px;
    margin-top: 18px;
}

.legend-card {
    grid-column: span 12;
    background: #fff;
    border: 1px solid rgba(10, 45, 107, 0.12);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 26px rgba(0, 0, 0, 0.06);
    transition: transform 180ms ease, box-shadow 180ms ease;
}

.legend-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 40px rgba(10, 45, 107, 0.12);
}

.legend-card__top {
    padding: 16px 16px 10px;
    background: linear-gradient(90deg, rgba(10, 45, 107, 0.06), rgba(255, 255, 255, 0));
}

.legend-card__tag {
    display: inline-block;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #3433cd;
    background: rgba(52, 51, 205, 0.10);
    border: 1px solid rgba(52, 51, 205, 0.18);
    padding: 6px 10px;
    border-radius: 999px;
    margin-bottom: 10px;
}

.legend-card__title {
    margin: 0 0 6px;
    color: #0a2d6b;
    font-weight: 900;
    font-size: 18px;
    letter-spacing: -0.01em;
}

.legend-card__lead {
    margin: 0;
    color: rgba(0, 0, 0, 0.65);
    font-size: 13px;
    font-weight: 600;
}

.legend-card__body {
    padding: 12px 16px 16px;
}

.legend-card__text {
    margin: 0;
    color: rgba(0, 0, 0, 0.72);
    font-size: 14px;
    line-height: 1.6;
}

.legend-card__footer {
    padding: 0 16px 16px;
}

.legend-card__btn {
    width: 100%;
    border: 0;
    border-radius: 14px;
    padding: 12px 14px;
    font-weight: 800;
    cursor: pointer;
    background: #0a2d6b;
    color: #fff;
    transition: filter 180ms ease;
}

.legend-card__btn:hover {
    filter: brightness(1.05);
}

/* Responsive */
@media (min-width: 640px) {
    .legend-card {
        grid-column: span 6;
    }
}

@media (min-width: 1024px) {
    .legend-card {
        grid-column: span 4;
    }
}

/* Modal */
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    display: grid;
    place-items: center;
    padding: 18px;
    z-index: 50;
}

.modal__panel {
    width: min(760px, 100%);
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 20px 70px rgba(0, 0, 0, 0.35);
}

.modal__head {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 14px;
    padding: 16px;
    background: linear-gradient(90deg, rgba(10, 45, 107, 0.08), rgba(255, 255, 255, 0));
    border-bottom: 1px solid rgba(10, 45, 107, 0.10);
}

.modal__tag {
    display: inline-block;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #3433cd;
    background: rgba(52, 51, 205, 0.10);
    border: 1px solid rgba(52, 51, 205, 0.18);
    padding: 6px 10px;
    border-radius: 999px;
    margin-bottom: 10px;
}

.modal__title {
    margin: 0 0 6px;
    color: #0a2d6b;
    font-weight: 900;
    font-size: 18px;
}

.modal__lead {
    margin: 0;
    color: rgba(0, 0, 0, 0.65);
    font-size: 13px;
    font-weight: 600;
}

.modal__close {
    border: 0;
    background: transparent;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    color: rgba(0, 0, 0, 0.55);
    padding: 0 6px;
}

.modal__content {
    padding: 16px;
    color: rgba(0, 0, 0, 0.78);
    line-height: 1.7;
    font-size: 15px;
}
</style>
