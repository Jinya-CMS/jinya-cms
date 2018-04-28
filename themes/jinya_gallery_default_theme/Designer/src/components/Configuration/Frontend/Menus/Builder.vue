<template>
    <jinya-editor>
        <jinya-form>
            <jinya-editor-pane>
            </jinya-editor-pane>
            <jinya-editor-pane>
                <draggable v-model="menu.children">
                    <jinya-menu-builder-group v-for="item in menu.children" :item="item"/>
                </draggable>
            </jinya-editor-pane>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaMenuBuilderGroup from "@/components/Configuration/Frontend/Menus/Builder/BuilderGroup";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import JinyaForm from "@/framework/Markup/Form/Form";
  import JinyaEditor from "@/framework/Markup/Form/Editor";
  import JinyaEditorPane from "@/framework/Markup/Form/EditorPane";
  import draggable from 'vuedraggable';

  export default {
    name: "Builder",
    components: {
      JinyaEditorPane,
      JinyaEditor,
      JinyaForm,
      JinyaMenuBuilderGroup,
      draggable
    },
    data() {
      return {
        menu: {
          name: '',
          id: -1,
          children: []
        }
      }
    },
    async mounted() {
      this.menu = await JinyaRequest.get(`/api/menu/${this.$route.params.id}`);
    }
  }
</script>

<style scoped>

</style>