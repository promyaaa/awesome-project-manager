<template>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <router-link :to="'/projects/' + $route.params.projectid " class="link-style inline-block" tag="h3">
                    <a>{{messageObject.project_title}}</a>
                </router-link>
                <router-link :to="'/projects/' + $route.params.projectid + '/messages'" class="link-style inline-block" tag="h4">
                    <a><i class="fa fa-long-arrow-right p-l-10 p-r-10" aria-hidden="true"></i>Message Board</a>
                </router-link>
            </div>
        </div>
        <div class="row lists">
            <div class="col-12">
                <!-- <h3>Message</h3> -->
                <div class="loading" v-if="loading">
                    <p>Loading . . .</p>
                </div>

                <div v-if="messageObject">
                    <div v-if="isShowEdit">
                        <router-link :to="'/projects/' + $route.params.projectid + '/messages/' + messageObject.ID + '/edit'" class="button button-default">
                            Edit
                        </router-link>
                        <span style="float:right" @click="deleteMessage(messageObject)">
                            <a style="color: #d54e21;cursor:pointer;">Delete</a>
                        </span> 
                    </div>
                    <br>
                    <div class="message-content">
                        <div class="text-center message-by">
                            <img :src="messageObject.avatar_url" class="small-round-image" alt="">
                            <p>
                                <i>posted by <strong>{{messageObject.user_name}}</strong>
                                at {{messageObject.formatted_created}}</i>
                            </p>
                        </div>
                        
                        <h1><strong>{{messageObject.message_title}}</strong></h1>
                        
                        <div class="message-desc" v-html="messageObject.message"></div>
                        
                        <div v-if="messageObject.files.length > 0">
                            <div v-for="file in messageObject.files" class="image-common">
                                <img :src="file.url" alt="" class="image-resize">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <comments :comments="messageObject.comments" type="message"></comments>
            </div>
        </div>
    </div>
</template>

<style>
    .image-common {
        padding:10px;
        border: 1px solid #eee;
        margin-bottom:20px;
        text-align:center;
    }
    .image-resize {
        max-width:100%;
        max-height:100%;
    }
    .message-content {
        padding: 20px 60px;
    }
    .message-desc {
        padding-left: 30px;
    }
    .message-by {
        margin-top: -40px;
        margin-bottom: 30px;
    }
</style>

<script>
    import store from '../store';
    import Comments from './partials/CommentsComponent.vue';
    export default {
        components: {
            'comments': Comments
        },
        data() {
            return {
                loading: false,
                localString: '',
                messageObject: ''
            }
        },
        computed: {
            isShowEdit: function() {
                var vm = this;
                return (vm.currentUser.roles[0] === 'administrator') || 
                        (vm.currentUser.data.ID === vm.messageObject.userID);
            }
        },
        methods: {
            // getScaledImageSize: function(srcWidth, srcHeight, maxWidth, maxHeight) {
            //     var ratio = 1;
            //     if (srcWidth > maxWidth || srcHeight > maxHeight) {
            //         ratio = Math.min(maxWidth / srcWidth, maxHeight / srcHeight);
            //     }

            //     return {
            //         width: srcWidth * ratio,
            //         height: srcHeight * ratio,
            //     };
            // },
            
            somethingCool: function() { // @click.native test method
                var vm = this,
                    messageKey;
                // messageKey = 'm' + vm.messageObject.ID + '-p' + vm.messageObject.projectID;
                // localStorage.setItem(messageKey, JSON.stringify(vm.messageObject));
                // console.log(JSON.parse(localStorage.getItem(messageKey)));
            },
            fetchMessage: function() {
                var vm = this,
                    projectID = vm.$route.params.projectid;
                vm.loading = true;
                
                var data = {
                    action: 'fpm-get-message-details',
                    project_id: vm.$route.params.projectid,
                    message_id: vm.$route.params.messageid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    console.log(resp);
                    if ( resp.success ) {
                        vm.messageObject = resp.data[0];
                    } else {
                        vm.$router.push({ 
                            path: `/?type=message&info=notfound` 
                        });
                    }
                });
            },

            editMessage: function() {
                var vm = this,
                    todo,
                    data = {
                        action : 'fpm-create-user',
                        nonce : fpm.nonce,
                        user_name: vm.username,
                        email: vm.email,
                        project_id: vm.$route.params.projectid
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    console.log(resp)
                    if ( resp.success ) {
                        if ( !resp.data.user.ID ) {
                            return;
                        }
                        var userObj = {};
                        userObj.ID = resp.data.user.ID;
                        userObj.display_name = vm.username;

                        vm.users.push(userObj);
                        vm.username = '';
                        vm.email = '';

                    } else {
                        vm.message = resp.data;
                    }
                });
            },

            deleteMessage( messageObj ) {
                if (confirm("Are you sure ??")) {
                    var vm = this,
                        projectID = +messageObj.projectID,
                        data = {
                            action : 'fpm-delete-message',
                            nonce : fpm.nonce,
                            message_id: messageObj.ID,
                            project_id: messageObj.projectID,
                            user_name: messageObj.user_name,
                            user_id: messageObj.userID,
                            message_title: messageObj.message_title
                        };
                        
                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            
                            vm.$router.push({ 
                                path: `/projects/${projectID}/messages` 
                            });

                        } else {
                            
                        }
                    });
                }
            }
        },

        created() {
            var vm = this;
            vm.fetchMessage();
            vm.currentUser = fpm.currentUserInfo;
            store.getLocalizeString().then(function(resp){
                // console.log(resp);
                vm.localString = resp.data.actions;
            });
        },

        mounted() {
            // console.log('Component mounted.')
        }
    }
</script>