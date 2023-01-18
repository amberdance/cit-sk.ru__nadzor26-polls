<template>
  <div class="wrapper" @click="$router.push('/home')">
    <div class="header">
      <div class="header__logo logo">
        <img src="../../assets/logo.png" alt="logo" class="logo__img" />
        <h3 class="logo__text">
          Упавление Ставропольского края - Государственная жилищная инспекция
        </h3>
      </div>
    </div>
  </div>
</template>

<style scoped>
.wrapper {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #122050;
  color: white;
  cursor: pointer;
  font-size: 15px;
}

img {
  height: 4rem;
}

h3 {
  font-weight: 300;
  padding: 0;
  margin: 0;
}

.header {
  padding: 1rem;
}

.header__logo {
  display: flex;
  align-items: center;
  width: 310px;
  gap: 0.5rem;
}

.header__logo h3 {
  font-size: 16px;
}

.header__label {
  width: 280px;
  text-align: center;
}
</style>
