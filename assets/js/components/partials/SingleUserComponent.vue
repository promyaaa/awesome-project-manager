<template>
    <div class="row">
        <div class="col-2">
            <div class="user-avatar">
                <img class="avatar small-round-image" :src="userObj.avatar_url" alt="...">
            </div>
        </div>
        <div class="col-10">
            <div class="user-info" v-if="!isShowEdit">
                <div style="float:right" v-if="isShowAction">
                    <span @click="showUserEdit(user)">
                        <a style="cursor: pointer">{{ i18n.edit }}</a>
                    </span>
                    <span class="trash" @click="removeUser(index)">| 
                        <a style="cursor: pointer;">{{ i18n.remove }}</a>
                    </span>
                </div>
                <span class="info">{{userObj.display_name}}</span>
                <span class="info">{{userObj.title}}</span>
                <span class="info">{{userObj.user_email}}</span>
            </div>
            <div v-if="isShowEdit" class="user-info">
                <input type="text" v-model="editUserEmail" :placeholder="i18n.email_placeholder">
                <input type="text" v-model="editUserTitle" :placeholder="i18n.title_placeholder">
                <br>
                <button class="button button-small button-primary" @click="updateUser" v-bind:disabled="isButtonDisabled">{{ i18n.update }}</button>
                <button class="button button-small button-default" @click="cancelUserEdit">{{ i18n.cancel }}</button>
            </div>
        </div>
    </div>
</template>
<style>

</style>
<script>
    export default {
        components: {
            
        },
        data() {
            return {
                userObj:'',
                userObjClone: '',
                isShowEdit: false,
                editUserEmail: '',
                editUserTitle: ''
            }
        },
        props: ['user', 'index', 'i18n'],
        computed: {
            isButtonDisabled() {
                return (this.userObj.user_email === this.editUserEmail) && 
                        (this.userObj.title === this.editUserTitle);  
            },
            isShowAction: function() {
                return this.currentUser.roles[0] === 'administrator';    
            }
        },
        methods: {
            showUserEdit( userObject ) {
                var vm = this;

                vm.userObjClone = JSON.parse(JSON.stringify(userObject));
                vm.isShowEdit = true;
                vm.editUserEmail = userObject.user_email;
                vm.editUserTitle = userObject.title;
            },

            cancelUserEdit( ) {
                var vm = this;

                vm.userObj = vm.userObjClone;
                vm.isShowEdit = false;
                vm.cloneObject = '';
            },
            removeUser( index ) {
                this.$emit('remove', index);
            },
            updateUser() {
                var vm = this,
                    projectid = vm.$route.params.projectid,
                    localUsersKey = projectid + '-users',
                    data = {
                        action : 'fpm-update-user',
                        nonce : fpm.nonce,
                        user_id: vm.userObj.ID,
                        email: vm.editUserEmail,
                        title: vm.editUserTitle
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    console.log(resp);
                    if ( resp.success ) {
                        vm.userObj.user_email = vm.editUserEmail;
                        vm.userObj.title = vm.editUserTitle;

                        localStorage.removeItem(localUsersKey);

                        vm.isShowEdit = false;
                        vm.editUserEmail = '';
                        vm.editUserTitle = '';

                    } else {
                        vm.message = resp.data;
                    }
                });
            }
        },
        created() {
            this.userObj = this.user;
            this.currentUser = fpm.currentUserInfo;
        },
        mounted() {
        }
    }
</script>