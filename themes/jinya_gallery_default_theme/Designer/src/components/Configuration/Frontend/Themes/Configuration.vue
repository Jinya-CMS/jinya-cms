<template>
    <jinya-form>
    </jinya-form>
</template>

<script>
  import JinyaForm from "@/components/Framework/Markup/Form/Form";
  import JinyaRequest from "@/components/Framework/Ajax/JinyaRequest";
  import Events from "@/components/Framework/Events/Events";
  import EventBus from "@/components/Framework/Events/EventBus";
  import DOMUtils from "@/components/Framework/Utils/DOMUtils";

  export default {
    name: "Configuration",
    components: {JinyaForm},
    data() {
      return {
        form: {
          title: '',
          groups: []
        }
      };
    },
    async mounted() {
      const form = await JinyaRequest.get(`/api/theme/${this.$route.params.name}/form/config`);
      this.form = form;
      EventBus.$emit(Events.header.change, form.title);
      DOMUtils.changeTitle(form.title);
    }
  }
</script>

<style scoped>

</style>