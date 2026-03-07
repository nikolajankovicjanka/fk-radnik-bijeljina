<template>
    <section class="cb">
        <div class="cb__container">

            <!-- Upravni odbor -->
            <header class="cb__header">
                <h2 class="cb__title">
                    {{ $t('pages.clubPage.cards.board.upravniOdbor') }}</h2>
                <span class="cb__accent" aria-hidden="true"></span>
            </header>

            <div class="cb__block">
                <div class="cb__label">
                    {{ $t('pages.clubPage.cards.board.predsednik') }}:
                </div>
                <div class="cb__value">Nedeljko Ćorić</div>
            </div>

            <div class="cb__block">
                <div class="cb__label">
                    {{ $t('pages.clubPage.cards.board.generalniDirektor') }}:
                </div>
                <div class="cb__value">Goran Janjić</div>
            </div>

            <div class="cb__block cb__block--list">
                <div class="cb__label">
                    {{ $t('pages.clubPage.cards.board.clanoviUprave') }}:
                </div>
                <ul class="cb__list">
                    <li>Nedeljko Ćorić
                        ({{ $t('pages.clubPage.cards.board.predsednik') }})
                    </li>
                    <li>Žarko Novaković</li>
                    <li>Milan Trninić</li>
                    <li>Mihailo Stevanović</li>
                    <li>Mladen Ristić</li>
                    <li>Goran Mirčić</li>
                    <li>Oleg Zupur</li>
                    <li>Miroslav Nikolić</li>
                    <li>Mario Đuran</li>
                </ul>
            </div>
            <header class="cb__header cb__header--spaced">
                <h2 class="cb__title">
                    {{ $t('pages.clubPage.cards.board.skupstinaKluba') }}</h2>
                <span class="cb__accent" aria-hidden="true"></span>
            </header>

            <div class="cb__block">
                <div class="cb__label">
                    {{ $t('pages.clubPage.cards.board.predsednik') }}:
                </div>
                <div class="cb__value">Duško Glišić</div>
            </div>

            <div class="cb__block cb__block--list">
                <div class="cb__label">
                    {{ $t('pages.clubPage.cards.board.potpredsjednici') }}:
                </div>
                <ul class="cb__list">
                    <li>Živorad Miražić</li>
                    <li>Nail Sobo</li>
                </ul>
            </div>

            <div class="cb__block cb__block--list">
                <div class="cb__label">
                    {{ $t('pages.clubPage.cards.board.clanoviSkupstine') }}:
                </div>
                <ul class="cb__list cb__list--cols">
                    <li>Vladimir Močević</li>
                    <li>Miroslav Jevtić</li>
                    <li>Bojan Vuković</li>
                    <li>Dražen Nikolić</li>
                    <li>Aleksandar Vasić</li>
                    <li>Vaso Arsenović</li>
                    <li>Vasko Todić</li>
                    <li>Slobodan Đorđić</li>
                    <li>Igor Lovrić</li>
                    <li>Jugoslav Despotović</li>
                    <li>Mladen Petrović</li>
                    <li>Igor Gagić</li>
                </ul>
            </div>
            <header class="cb__header cb__header--spaced">
                <h2 class="cb__title">
                    {{ $t('pages.clubPage.cards.board.nadzorniOdbor') }}</h2>
                <span class="cb__accent" aria-hidden="true"></span>
            </header>

            <div class="cb__block cb__block--list">
                <div class="cb__label">
                    {{ $t('pages.clubPage.cards.board.clanovi') }}:
                </div>
                <ul class="cb__list">
                    <li>Milan Lazarević</li>
                    <li>Mladen Đukić</li>
                    <li>Petar Ilić</li>
                </ul>
            </div>
            <header class="cb__header cb__header--spaced">
                <h2 class="cb__title">
                    {{ $t('pages.clubPage.cards.board.skautingSluzba') }}</h2>
                <span class="cb__accent" aria-hidden="true"></span>
            </header>

            <ul class="cb__list">
                <li>Filip Vujić -
                    {{ $t('pages.clubPage.cards.board.sefSkautingSluzbe') }}
                </li>
                <li>Nikola Janković</li>
                <li>Bojan Vuković</li>
            </ul>

        </div>
    </section>
</template>

<script setup lang="ts">
</script>

<style scoped>
</style>
