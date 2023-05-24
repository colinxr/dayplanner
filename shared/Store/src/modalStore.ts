import { ref } from 'vue'
import { defineStore } from 'pinia'
import { ModalStateI, VueComponent } from './types'

const useModalStore = defineStore('modalStore', () => {
  const state = ref<ModalStateI>({ component: null, props: {} })

  const openModal = (payload: {component: VueComponent, props: Object }) => {
    const { component, props } = payload

    state.value.component = component
    state.value.props = props || {}
  }

  const closeModal = () => {
    state.value = { component: null, props: {} }
  }

  return {
    state,
    openModal,
    closeModal,
  }
})

export default useModalStore
