<template>
  <div id="app">
    <router-view />
  </div>
</template>

<style>
@import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");
* {
  box-sizing: border-box;
}
body {
  font-family: "Roboto", sans-serif;
  margin: 0;
  padding: 0;
}
.content_wrapper {
  background-image: url("./assets/background.png");
  background-repeat: no-repeat;
  background-position: right;
  height: 100vh;
  background-attachment: fixed;
  padding: 0 !important;
}
.d-flex {
  display: flex !important;
}
.pagination_wrapper {
  display: flex;
  justify-content: center;
}
.justify-end {
  display: flex !important;
  justify-content: flex-end !important;
}
.align-center {
  display: flex !important;
  align-self: center !important;
  align-items: center !important;
}
.align-end {
  display: flex !important;
  align-self: flex-end !important;
  align-items: flex-end !important;
}
.justify-arround {
  display: flex !important;
  justify-content: space-around !important;
}
.flex-column {
  flex-direction: column !important;
}
.text-center {
  text-align: center !important;
}
.text-left {
  text-align: left !important;
}
.text-right {
  text-align: right !important;
}
.text-gray {
  color: gray;
}
.table_wrapper {
  padding: 1rem 0;
}
.table_wrapper th {
  color: #ffffff;
  font-weight: normal;
  background-color: #23497b !important;
  border-right: 1px #ffffff solid;
  padding: 0.6rem 0 !important;
  text-transform: uppercase;
  font-size: 12px;
}
.table_wrapper td {
  color: black;
}
.el-table .cell {
  word-break: break-word !important;
}
.el-drawer__body {
  overflow: auto;
}
.el-drawer__body .content_wrapper {
  margin: 0;
}
table .success_row {
  background-color: #a2ecb6 !important;
}
table .warning_row {
  background-color: #f0ff99 !important;
}
table .danger_row {
  background-color: #ff9494 !important;
}
</style>
