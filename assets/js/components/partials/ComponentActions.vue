<template>
    <div>
        <div class="action-icon" v-if="!isShowActions" @click="showAction">
            <i class="fa fa-ellipsis-h"></i>
        </div>
        <transition
            name="custom-transition"
            enter-active-class="animated-fast slideInRight"
            leave-active-class="animated-fast slideOutRight"
        >
            <div class="div-to-slide" v-if="isShowActions">
                <div>
                    <div @click="showAction" class="hide-action-div">
                        <i class="fa fa-remove action-close"></i>
                    </div>
                    <div style="clear:right;">
                        <ul style="padding:10px 10px 0px">

                            <slot></slot>

                        </ul>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    export default{
        data() {
            return {
                isShowActions: false
            }
        },
        methods: {
            showAction() {
                this.isShowActions = !this.isShowActions;
            },
        },
        created() {
            var vm = this;
            // Event.$on('toggle-actions', function() {
            //     vm.showAction();
            // });
        }
    }
</script>

<style>
    li.action-item {
        border-bottom: 1px solid hsla(0, 0%, 0%, 0.1);
        padding: 5px;
        transition: background-color 0.2s;
        color: #fff;
        cursor: pointer;
    }
    /*li.action-item a {
        color: #fff;
        cursor: pointer;
        text-decoration: none;
    }*/
    li.action-item i {
        padding-right: 5px;
    }
    li.action-item:hover {
        background-color: rgba(0,0,0,0.25);
    }
    .action-close {
        float: right;
        margin-top: 10px;
        margin-right: 10px;
        padding:5px 9px;
        border: 1px solid #fff;
        border-radius: 40px;
        display: block;
        cursor: pointer;
    }
    .action-icon {
        position:absolute;
        top: -5px;
        right: -15px;
        padding:5px 9px;
        border:1px solid #eee;
        border-radius:40px;
        cursor: pointer;
    }
    .div-to-slide {
        position: absolute;
        top: -15px;
        right: -25px;
        background: #0073AA;
        overflow: hidden;
        color: #fff;
        width: 30%;
        z-index: 999;
    }
    .hide-action-div {
        /*display: block;*/
        color:#fff;
        text-align: right;
        /*width:100%*/
    }
</style>