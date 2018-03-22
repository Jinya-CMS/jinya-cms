<template>
    <transition name="overlay" enter-class="is--entering" leave-to-class="is--leaving">
        <section class="jinya-modal__overlay" @click.self="$emit('close')">
            <dialog class="jinya-modal-dialog" open>
                <header class="jinya-modal-dialog__title" :v-jinya-message="title"></header>
                <section class="jinya-modal-dialog__content">
                    <slot/>
                </section>
                <footer class="jinya-modal-dialog__footer">
                    <slot name="buttons-left"/>
                    <slot name="buttons-right"/>
                </footer>
            </dialog>
        </section>
    </transition>
</template>

<script>
  export default {
    name: "jinya-modal",
    props: {
      title: {
        type: String,
        required: true
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-modal__overlay {
        background: scale_color($gray-300, $alpha: 80%);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 99999;
        transition: opacity 0.3s;
        opacity: 1;

        &.is--entering,
        &.is--leaving {
            opacity: 0;

            .jinya-modal-dialog {
                opacity: 0;
            }
        }

        .jinya-modal-dialog {
            padding: 0;
            transition-delay: 0.2s;
            transition: opacity 0.3s;
            border: none;
            z-index: 100000;
            max-height: 30em;
            min-height: 20em;
            width: 35em;
            box-shadow: 0 0 10px 0 scale_color($primary, $alpha: 20%);
            border-radius: 10px;
            opacity: 1;

            &__title {
                background: $primary;
                color: $primary-lighter;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }

            &__content {
                background: $white;
            }

            &__footer {
                border-bottom-left-radius: 10px;
                border-bottom-right-radius: 10px;
                background: $primary;
            }
        }
    }
</style>