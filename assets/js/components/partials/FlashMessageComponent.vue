<template>
    <transition
                name="custom-transition"
                enter-active-class="animated-fast slideInRight"
                leave-active-class="animated fadeOut"
            >
        <div class="flash" v-bind:class="flashtype" v-if="message">
            <div>
                <i class="fa" v-bind:class="icon"></i>
                <span>{{message}}</span>
                <span style="float:right;cursor: pointer;margin-left:20px" @click="removeFlash">x</span>
            </div>
        </div>
    </transition>
</template>

<script>
    export default{
        props: [],
        components: {

        },
        data() {
            return {
                flashtype: '',
                message: '',
                icon: ''
            }
        },
        watch: {
            '$route' (to, from) {
                var vm = this;
                switch(to.query.op) {
                    case 'rf': //read failed
                        vm.fireEvent({
                            message: vm.$route.query.item + ' not found',
                            flashtype:'danger',
                            iconfont: 'fa-remove'
                        });
                        break;
                    case 'ds': //delete success
                        vm.fireEvent({
                            message: vm.$route.query.item + ' deleted successfully',
                            flashtype: 'success',
                            iconfont: 'fa-check'
                        });
                        break;
                    case 'cs': //create success
                        vm.fireEvent({
                            message: vm.$route.query.item + ' created successfully',
                            flashtype: 'success',
                            iconfont: 'fa-check'
                        });
                        break;
                    case 'us': // update success
                        vm.fireEvent({
                            message: vm.$route.query.item + ' updated successfully',
                            flashtype: 'success',
                            iconfont: 'fa-check'
                        });
                        break;
                    case 'ps': // post success
                        vm.fireEvent({
                            message: vm.$route.query.item + ' posted successfully',
                            flashtype: 'success',
                            iconfont: 'fa-check'
                        });
                        break;
                    default:
                        break;
                }
                
            }
        },
        methods: {
            removeFlash() {
                this.flashtype = '';
                this.message = '';
                this.icon = '';
            },
            fireEvent( data ) {
                Event.$emit('flash', {
                    message: data.message,
                    flashtype: data.flashtype,
                    iconfont: data.iconfont
                });
            }
        },
        created() {
            var vm = this,
                duration,
                timer,
                messageText = '';
                
            Event.$on('flash', function(data) {
                if ( timer ) {
                    clearTimeout( timer );
                }
                messageText = data.message
                
                vm.message = messageText.charAt(0).toUpperCase() + messageText.slice(1);
                vm.flashtype = data.flashtype;
                vm.icon = data.iconfont;
                duration = data.duration || 3000

                timer = setTimeout(function(){
                    vm.removeFlash();
                }, duration);
            });
        },
    }
</script>

<style>
    .flash {
        padding: 10px;
        border-radius: 4px;
        text-align: center;
        border: 1px solid transparent;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.2);
        position: fixed;
        z-index: 9999;
        top: 50px;
        right: 12px;
    }
    .info {
        background-color: #d9edf7;
        border-color: #bce8f1;
        color: #3a87ad;
    }
    .warning {
        background-color: #fcf8e3;
        border-color: #fbeed5;
        color: #c09853;
    }
    .success {
        background-color: #dff0d8;
        border-color: #d6e9c6;
        color: #468847;
    }
    .danger {
        background-color: #f2dede;
        border-color: #eed3d7;
        color: #b94a48;
    }
</style>