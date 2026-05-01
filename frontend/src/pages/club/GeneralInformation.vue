<template>
  <section class="gi-fluid">
    <div class="gi-section gi-section--light">
      <div class="gi-inner">
        <h2 class="gi-title">{{
            $t('pages.clubPage.cards.general.title')
          }}</h2>
        <div class="gi-block">
          <h3 class="gi-subtitle">
            {{ $t('pages.clubPage.cards.general.adresa') }}</h3>
          <address class="gi-address">
            <span>Balkanska BB</span>
            <span>Gradski stadion Bijeljina</span>
            <span>Bijeljina, 76300</span>
            <span>Republika Srpska, BiH</span>
          </address>
        </div>
        <ul class="gi-facts">
          <li><strong>{{
              $t('pages.clubPage.cards.general.nadimak')
            }}:</strong> Plavo-bijeli
          </li>
          <li><strong>{{
              $t('pages.clubPage.cards.general.osnovan')
            }}:</strong> 1945
          </li>
          <li><strong>{{
              $t('pages.clubPage.cards.general.kapacitetStadiona')
            }}:</strong> 6.000
          </li>
          <li><strong>{{
              $t('pages.clubPage.cards.general.teren')
            }}:</strong> 105 x 68 metara
          </li>
        </ul>
      </div>
    </div>

    <div class="gi-section gi-section--brand">
      <div class="gi-inner gi-contact">
        <div class="gi-contact__text">
          <h2 class="gi-title gi-title--inverse">
            {{ $t('pages.clubPage.cards.general.kontakt') }}</h2>

          <p class="gi-text gi-text--inverse">
            {{
              $t('pages.clubPage.cards.general.opsteInformacije')
            }}:<br>
            <a href="tel:+38755201338"> +387 55 201 338</a>
            <span><br>  (Pon–Pet, 09:00–17:00)</span>
          </p>
          <p class="gi-text gi-text--inverse">
            {{ $t('pages.clubPage.cards.general.ulaznice') }}:
            <a href="tel:+38755201338"> +387 55 201 338</a>
            <span><br>(Pon–Pet, 10:00–17:00)</span>
          </p>
          <p class="gi-text gi-text--inverse">
            E-mail:
            <a href="mailto:fkradnikbijeljina@gmail.com">fkradnikbijeljina@gmail.com</a>
          </p>
        </div>
        <div class="gi-contact__logo">
          <img
              src="/logo/FK_Radnik_logo.png"
              alt="FK Radnik Bijeljina"
          />
        </div>
      </div>
    </div>
    <ClubDocumentsSection />
  </section>
</template>

<script setup lang="ts">
import ClubDocumentsSection from './ClubDocumentsSection.vue'
</script>

<style scoped>
</style>