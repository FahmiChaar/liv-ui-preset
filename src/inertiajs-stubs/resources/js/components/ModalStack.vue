<template>
    <div class="modals-wrapper" :class="{'modals-wrapper-visible': hasModals}">
        <transition-group name="list" tag="div" v-on:after-leave="afterCloseAnimationDone">
            <div class="inner-wrapper" v-for="(modal, index) in modalStack" :key="modal.component + '-' + index">
                <div class="backdrop" @click="closeModal(modal.component, modal.options.persist)"></div>
                <div class="modal-container" 
                    :class="{'modal-persist-animated': persistAnimation}"
                    :style="{'max-width': modal.options.maxWidth}">
                    <component :is="modal.component" v-bind="modal.props"></component>
                </div>
            </div>
        </transition-group>
    </div>
</template>

<script>

/*
    Modal options
    {
        maxWidth // defalut width 80%,
        persist // defautl false
    }
*/

export default {
    data() {
        return {
            modalStack: [],
            hasModals: false,
            persistAnimation: false,
            persistAnimationTimeout: null,
            closeModalOnInertiaSuccess: true,
            removeBeforeEventListener: () => {}
        }
    },
    beforeMount() {
        this.$inertia.visitInModal = (url, closeOnSuccess = true) => {
            this.closeModalOnInertiaSuccess = closeOnSuccess;
            this.visitInModal(url);
        }
        this.$inertia.on("success", (event) => {
            this.$root.$emit('modal:inertia:success', event);
            // remove the 'X-Inertia-Modal' and 'X-Inertia-Modal-Redirect-Back' headers for future requests
            this.removeBeforeEventListener()
            if (this.closeModalOnInertiaSuccess) {
                this.closeModal();
            }
        })
    },
    created: function() {
        document.addEventListener("keydown", (e) => {
            if (this.hasModals && e.keyCode == 27) {
                const activeModal = this.modalStack[this.modalStack.length-1]
                this.closeModal(activeModal.component, activeModal.options.persist)
            }
        })
        this.$root.$on('modal::show', data => {
            this.showModal(data)
        })
        this.$root.$on('modal::close', component => {
            this.closeModal(component)
        })
    },
    methods: {
        afterCloseAnimationDone() {
            if (!this.modalStack.length) {
                this.hasModals = false
                document.removeEventListener("keydown", (e) => {})
            }
        },
        showModal(data) {
            if (typeof data === 'string') {
                this.modalStack.push({component: data, props: {}, options: {}})
                this.hasModals = true
            }else if (typeof data === 'object') {
                data.props = data.props || {}
                data.options = data.options || {}
                this.modalStack.push(data)
                this.hasModals = true
            }else {
                this.hasModals = false
                throw 'Data passed to Modal component should be of type string|object'
            }
        },
        closeModal(component, persist) {
            if (!persist) {
                if (component) {
                    // I reverse modalStack to findLastIndex
                    const componentIndex = this.modalStack.reverse().findIndex(m => m.component === component)
                    if (componentIndex > -1) {
                        // const index = this.modalStack.length - componentIndex
                        this.modalStack.splice(componentIndex, 1)
                    }else {
                        if (this.modalStack.length == 1) {
                            this.modalStack = []
                        }else {
                            throw `No modal mounted this component, ${component}`
                        }
                    }
                }else {
                    this.modalStack.reverse().splice(0, 1)
                }
            }else {
                if (this.persistAnimationTimeout) {
                    this.persistAnimation = false
                    clearTimeout(this.persistAnimationTimeout)
                }
                this.persistAnimation = true
                this.persistAnimationTimeout = setTimeout(() => {
                    this.persistAnimation = false
                }, 160);
            }
        },
        visitInModal(url) {
            return new Promise(async (resolve) => {
                const Inertia = this.$inertia
                const { data: { component, props, options } } = await axios({
                    method: 'get',
                    url,
                    headers: {
                        Accept: "text/html, application/xhtml+xml",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-Inertia": true,
                        "X-Inertia-Modal": true,
                        "X-Inertia-Version": this.$page.version,
                    },
                })
                const resolvedComponent = await Inertia.resolveComponent(component)
                this.removeBeforeEventListener = Inertia.on("before", (event) => {
                    // make sure the backend knows we're requesting from within a modal
                    event.detail.visit.headers["X-Inertia-Modal"] = true;
                    // event.detail.visit.headers[
                    //     "X-Inertia-Modal-Redirect-Back"
                    // ] = true;
                })
                this.showModal({
                    component: resolvedComponent,
                    props,
                    options
                })
                resolve(true)
            })
        }
    },
    destroyed: function() {
        document.removeEventListener("keydown", e => {})
    }
}
</script>

<style scoped>
    .list-enter-active, .list-leave-active {
        transition: all .4s;
    }
    .list-enter, .list-leave-to /* .list-leave-active below version 2.1.8 */ {
        opacity: 0;
    }
    .list-enter .modal-container, .list-leave-active .modal-container {
        transform: translateY(30px);
        opacity: 0;
    }
    .modals-wrapper {
        position: fixed;
        z-index: -1;
        top: 0;
        left: 0;
        background: transparent;
    } 
    .modals-wrapper-visible {
        z-index: 20;
        width: 100%;
        height: 100%;
    }
    .backdrop {
        position: fixed;
        z-index: 20;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.45);
    }
    .inner-wrapper {
        position: absolute;
        z-index: 21;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-container {
        position: relative;
        z-index: 21;
        background: transparent;
        max-width: 80%;
        max-height: 90%;
        width: 80%;
        box-shadow: 0 11px 15px -7px rgba(0,0,0,.2), 0 24px 38px 3px rgba(0,0,0,.14), 0 9px 46px 8px rgba(0,0,0,.12);
        border-radius: 2px;
        margin: 24px;
        overflow-y: auto;
        pointer-events: auto;
        transition: all 0.4s;
    }
    .modal-persist-animated {
        -webkit-animation-duration: .15s;
        animation-duration: .15s;
        -webkit-animation-name: persist-animation;
        animation-name: persist-animation;
        -webkit-animation-timing-function: cubic-bezier(.25,.8,.25,1);
        animation-timing-function: cubic-bezier(.25,.8,.25,1);
    }
    @keyframes persist-animation{
        0% {
            -webkit-transform: scale(1);
            transform: scale(1);
        }
        50% {
            -webkit-transform: scale(1.03);
            transform: scale(1.03);
        }
        100% {
            -webkit-transform: scale(1);
            transform: scale(1);
        }
    }
</style>