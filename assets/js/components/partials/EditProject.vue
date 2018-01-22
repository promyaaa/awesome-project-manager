<template>
    <div class="container">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8 project-edit-content">
                <h1 class="ellipsis-90">Edit <span class="text-highlight">{{project.project_title}}</span></h1>
                <div class="project-edit-form">
                    <div>
                        <input type="text" v-model="projectTitle" class="form-control">
                    </div>
                    <div>
                        <textarea  v-model="projectDesc" class="form-control" rows="5"></textarea>
                    </div>
                    <br>
                    <button class="button button-primary" @click="updateProject">Update</button>
                </div>
                <router-link :to="'/projects/' + $route.params.projectid">Back to summary</router-link>
            </div>
        </div>
    </div>
</template>

<style>
    .project-edit-content {
        padding: 40px 50px;
        background: #fff;
        border-radius: 5px;
        text-align: center;
    }
    .project-edit-form {
        padding: 30px 5px;
    }
    .text-highlight {
        background-color: #faf785;
        margin-left: 3px;
        padding: 0 0.2em;
        border-radius: 4rem 2rem 4.2rem 1.1rem;
        box-shadow: 0.2em 0 0 #faf785, -0.2em 0 0 #faf785
    }
</style>

<script>
    export default {
        data() {
            return {
                project: '',
                currentUser: '',
                projectTitle: '',
                projectDesc: ''
            }
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },
        methods: {
            updateProject() {
                var vm = this,
                    project,
                    projectID = vm.$route.params.projectid,
                    data = {
                        action : 'fpm-insert-project',
                        nonce : fpm.nonce,
                        title: vm.projectTitle,
                        description: vm.projectDesc,
                        project_id: projectID,
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    console.log(resp);
                    if ( resp.success ) {

                        // messageID = resp.data.messageInfo.ID;
                        // projectID = data.project_id;
                        vm.$router.push({ path: `/projects/${projectID}` })
                        // resp.data.messageInfo.message = vm.message;
                        // vm.messages.unshift(resp.data.messageInfo);
                        // vm.message = '';
                        // vm.messageTitle = '';
                    } else {
                        // vm.message = resp.data;
                    }
                });
            },
            fetchProjectInfo: function() {
                var vm = this;
                // vm.loading = true;
                
                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    console.log(resp);
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                        vm.projectTitle = vm.project.project_title;
                        vm.projectDesc = vm.project.project_desc;
                    }
                });
            },
        },
        created() {
            this.fetchProjectInfo();
            this.currentUser = fpm.currentUserInfo;
        }
    }
</script>