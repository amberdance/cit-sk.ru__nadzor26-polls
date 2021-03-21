<template>
  <div :class="$style.wrapper">
    <div>Рейтинг управляющих компаний Ставропольского края</div>
    <img src="../../assets/logo.png" />
  </div>
</template>

<style module>
.wrapper {
  width: 100%;
  display: flex;
  align-items: center;
  text-transform: uppercase;
  padding: 1rem 0.8rem;
  background-position: bottom;
  background-size: cover;
  background-repeat: no-repeat;
  background-color: #23497b;
  min-height: 60px;
  color: white;
}
.wrapper img {
  height: 55px;
  width: auto;
  margin-left: auto;
}
</style>
