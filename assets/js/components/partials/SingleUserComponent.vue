<template>
    <div class="row">
        <div class="col-2">
            <div class="user-avatar">
                <img class="avatar small-round-image" :src="user.avatar_url" alt="...">
            </div>
        </div>
        <div class="col-10">
            <div class="user-info" v-if="!isShowEditForm">
                <div style="float:right" v-if="isShowAction">
                    <span @click="showUserEdit(user)">
                        <a style="cursor: pointer"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    </span>
                    <span v-if="isShowDelete" class="trash" @click="removeUser(index)">| 
                        <a style="cursor: pointer;"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </span>
                </div>
                <span class="info"><strong>{{user.display_name}}</strong></span>
                <span class="info">{{user.title}}</span>
                <span class="info"><i>{{user.user_email}}</i></span>
            </div>
            <div v-if="isShowEditForm" class="user-info add_form_style">
                <input class="form-control" type="text" v-model="editUserEmail" :placeholder="i18n.email_placeholder">
                <input class="form-control" type="text" v-model="editUserTitle" :placeholder="i18n.title_placeholder">
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
                userClone: '',
                isShowEditForm: false,
                editUserEmail: '',
                editUserTitle: ''
            }
        },
        props: ['user', 'index', 'i18n'],
        computed: {
            isButtonDisabled() {
                return (this.user.user_email === this.editUserEmail) && 
                        (this.user.title === this.editUserTitle);  
            },
            isShowAction() {
                return this.currentUser.roles[0] === 'administrator';    
            },
            isShowDelete() {
                return this.currentUser.roles[0] === 'administrator' && parseInt(this.currentUser.ID) !== parseInt(this.user.ID);  
            },
        },
        methods: {
            showUserEdit( userect ) {
                var vm = this;

                vm.userClone = JSON.parse(JSON.stringify(userect));
                vm.isShowEditForm = true;
                vm.editUserEmail = userect.user_email;
                vm.editUserTitle = userect.title;
            },

            cancelUserEdit( ) {
                var vm = this;

                // vm.user = vm.userClone;
                vm.isShowEditForm = false;
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
                        user_id: vm.user.ID,
                        email: vm.editUserEmail,
                        title: vm.editUserTitle
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.user.user_email = vm.editUserEmail;
                        vm.user.title = vm.editUserTitle;

                        localStorage.removeItem(localUsersKey);

                        vm.isShowEditForm = false;
                        vm.editUserEmail = '';
                        vm.editUserTitle = '';

                    } else {
                        vm.message = resp.data;
                    }
                });
            }
        },
        created() {
            // this.user = this.user;
            this.currentUser = fpm.currentUserInfo;
        },
        mounted() {
        }
    }
</script>