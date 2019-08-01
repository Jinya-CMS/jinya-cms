<template>
    <transition enter-class="is--entering" leave-to-class="is--leaving" name="overlay">
        <div :class="{'is--fullscreen': isFullscreen}" @click.self="() => { if (!this.loading) $emit('close') }"
             class="jinya-modal__overlay">
            <dialog :class="additionalClasses" class="jinya-modal-dialog" open>
                <header class="jinya-modal-dialog__title" v-jinya-message="title"></header>
                <div class="jinya-modal-dialog__message">
                    <slot name="message"/>
                </div>
                <div class="jinya-modal-dialog__loader" v-if="loading">
                    <jinya-loader :loading="loading"/>
                </div>
                <div class="jinya-modal-dialog__content" v-if="!loading">
                    <slot/>
                </div>
                <footer class="jinya-modal-dialog__footer">
                    <div class="jinya-modal-dialog__buttons--start">
                        <slot name="buttons-left"/>
                    </div>
                    <div class="jinya-modal-dialog__buttons--end">
                        <slot name="buttons-right"/>
                    </div>
                </footer>
            </dialog>
        </div>
    </transition>
</template>

<script>
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';

  export default {
    name: 'jinya-modal',
    components: { JinyaLoader },
    props: {
      title: {
        type: String,
        required: true,
      },
      loading: {
        type: Boolean,
        default() {
          return false;
        },
      },
      modalModifiers: {
        default() {
          return {};
        },
      },
      isFullscreen: {
        type: Boolean,
        default() {
          return false;
        },
      },
    },
    computed: {
      additionalClasses() {
        return {
          'is--fullscreen': this.isFullscreen,
          ...this.modalModifiers,
        };
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-modal__overlay {
        background: transparentize($gray-300, 0.2);
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

        &.is--fullscreen {
            align-items: start;
            padding-top: 3rem;
        }

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
            max-width: 50em;
            box-shadow: 0 0 10px 0 scale_color($primary, $alpha: 20%);
            border-radius: 10px;
            opacity: 1;

            &.is--fullscreen {
                max-width: 95%;
                width: 95%;
            }

            &__title {
                background: $primary;
                color: $primary-lighter;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                font-size: 2rem;
                padding: 1rem 2rem;
            }

            &__loader {
                display: flex;
                height: 15em;
            }

            &__content {
                background: $white;
                max-height: 40em;
                padding: 2em;
                overflow-y: auto;
            }

            &__footer {
                border-bottom-left-radius: 10px;
                border-bottom-right-radius: 10px;
                background: $primary;
                padding: 2em;
                display: flex;
                justify-content: space-between;
            }

            &__message {
                width: 100%;
            }

            &__buttons {
                &--start {
                    align-self: flex-start;

                    .jinya-modal__button {
                        margin-right: 1rem;
                    }
                }

                &--end {
                    align-self: flex-end;

                    .jinya-modal__button {
                        margin-left: 1rem;
                    }
                }
            }
        }
    }
</style>
