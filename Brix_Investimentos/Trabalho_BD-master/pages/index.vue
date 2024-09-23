<template>
  <div class="main-container">
    <v-container>
      <v-row class="fill-height">
        <!-- Coluna da esquerda com a logo e texto -->
        <v-col cols="12" md="6" class="left-column">
          <div class="branding">
            <img src="@/assets/image.png" alt="Logo" class="logo" />
            <div>
              <span class="brix">Brix</span>
              <span class="investimentos">Investimentos</span>
            </div>
          </div>
          <p class="description">
            Desbloqueie o potencial dos seus investimentos com nossa plataforma  revolucionária. Desde iniciantes até veteranos, oferecemos as  ferramentas e insights necessários para maximizar seus lucros. Invista  no seu futuro financeiro agora mesmo e comece a colher os frutos amanhã!
          </p>
          <div class="icon-wrapper">
            <v-icon class="icon">mdi-check-circle</v-icon>
            <span class="icon-text">100% gratuito e funcional</span>
          </div>
        </v-col>
        <!-- Coluna da direita com o formulário -->
        <v-col cols="12" md="6" class="form-column">
          <RegisterCard />
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;800&display=swap');

.main-container {
  /* Ajusta a margem superior para 307px */
  display: flex;
}

.fill-height {
  height: 100%;
}

.left-column {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: center;
}

.form-column {
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.branding {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
}

.logo {
  height: 50px;
  width: 50px;
  margin-right: 7px;
}

.brix {
  color: black;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
  margin-right: 7px;
}

.investimentos {
  color: #4A3AFF;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
}

.description {
  font-size: 1.1rem;
  font-family: 'Montserrat', sans-serif;
  font-weight: 300;
  margin-top: 98px;
  text-align: center;
}

.icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 65px;
}

.icon {
  font-size: 1.5rem;
  color: #4A3AFF;
  margin-right: 0.5rem;
}

.icon-text {
  font-size: 1.1rem;
  color: #4A3AFF;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
}
</style>
