<template>
    <div class="modal" tabindex="-1" id="modal">
        <div class="modal-dialog modal-lg" :class="{ 'modal-dialog-centered': isCenter }">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ props.modalTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <slot name="body" />
                </div>
                <div class="modal-footer">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
const props = defineProps({
    modalTitle: {
        type: String,
        default: ""
    },
    modalId: {
        type: String,
        default: ""
    },
    isCenter: {
        type: Boolean,
        default: false
    }
})
</script>
<style scoped>
</style>