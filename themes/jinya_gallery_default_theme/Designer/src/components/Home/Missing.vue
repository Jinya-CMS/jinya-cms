<template>
    <h1 v-jinya-message="'home.missing.header'">Sorry, I am missing</h1>
</template>

<script>
  import DOMUtils from "@/framework/Utils/DOMUtils";
  import Translator from "@/framework/i18n/Translator";
  import EventBus from "@/framework/Events/EventBus";
  import Events from "@/framework/Events/Events";

  export default {
    name: "missing",
    mounted() {
      DOMUtils.changeTitle(Translator.message('home.missing.header'));
      EventBus.$emit(Events.header.change, Translator.message('home.missing.header'));
    }
  }
</script>

<style scoped>

</style>