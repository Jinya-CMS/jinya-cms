<template>
    <section class="jinya-card__item">
        <span class="jinya-card__header">{{header}}</span>
        <div class="jinya-card__body">
            <slot/>
        </div>
        <div class="jinya-card__footer">
            <slot name="footer"/>
        </div>
    </section>
</template>

<script>
  export default {
    name: "jinya-card",
    props: {
      header: {
        type: String,
        required: true
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-card__item {
        flex: auto;
        margin: 1em;
        box-shadow: 0 0 1px 0 $primary;
        background: $white;
        border-radius: 10px;
        transition: box-shadow 0.3s;
        display: inline-block;
        white-space: normal;
        vertical-align: top;

        &:only-child {
            .jinya-card__body {
                height: 30em;
            }
        }

        &:hover {
            border: none;
            box-shadow: 0 0 10px 0 scale_color($primary, $alpha: 80%);
        }

        .jinya-card__header {
            @extend h3;
            display: block;
            width: 100%;
            text-align: center;
            padding: 5px;
            border-bottom: 1px solid $primary;
        }

        .jinya-card__body {
            height: 15em;
            width: auto;
        }

        .jinya-card__footer {
            width: 100%;
            display: flex;
            border-radius: 0 0 10px 10px;
        }
    }
</style>