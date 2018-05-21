<template>
    <div>
        <div class="col-12">
            <div class="help-section">
                <slot></slot>    
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        props: [],
        components: {

        },
        data() {
            return {

            }
        },
        directives: {

        },
        methods: {

        },
        created() {

        },
        mounted() {

        }
    }
</script>

<style>
    .help-section {
        color: #6b6b6b;
        background: #fffcd5;
        border: 1px dashed #e7dca4;
        padding: 5px 10px;
        border-radius: 4px;
    }
</style>