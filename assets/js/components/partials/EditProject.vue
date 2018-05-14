<template>
    <div class="container">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8 project-edit-content">
                <h1 class="ellipsis-90">{{i18n.edit}} <i style="color:#6d6d6d;">{{project.project_title}}</i></h1>
                <div class="project-edit-form">
                    <div>
                        <input type="text" v-model="projectTitle" class="form-control">
                    </div>
                    <div>
                        <textarea  v-model="projectDesc" class="form-control" rows="5"></textarea>
                    </div>
                    <br>
                    <button class="button button-primary" @click="updateProject">{{i18n.update}}</button>
                    <router-link :to="'/projects/' + $route.params.projectid" class="button">
                        {{i18n.cancel}}
                    </router-link>
                </div>
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
    import store from '../../store';
    export default {
        data() {
            return {
                i18n: {},
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
                    if ( resp.success ) {
                        vm.$router.push({ path: `/projects/${projectID}` });
                    } else {
                        // vm.message = resp.data;
                    }
                });
            },
            fetchProjectInfo: function() {
                var vm = this,
                    projectID = vm.$route.params.projectid;
                // vm.loading = true;
                
                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // vm.loading = false;
                    if ( resp.success ) {
                        if( vm.currentUser.data.ID === resp.data[0].userID ) {
                            vm.project = resp.data[0];
                            vm.projectTitle = vm.project.project_title;
                            vm.projectDesc = vm.project.project_desc;
                        } else {
                            vm.$router.push({ path: `/projects/${projectID}?type=unauthorized` });
                        }
                        
                    }
                });
            },
        },
        created() {
            var vm = this;
            vm.fetchProjectInfo();
            vm.currentUser = fpm.currentUserInfo;
            store.setLocalization( 'fpm-get-project-edit-local-data' ).then( function( data ) {
                vm.i18n = data;
            });
        }
    }
</script>