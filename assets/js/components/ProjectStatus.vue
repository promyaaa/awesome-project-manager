<template>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center postbox">
                <div class="row">
                    <div class="col-2"></div>
                    <div class="col-8">
                        <h1 style="border-bottom: 2px solid grey;"><strong>Delete this project?</strong></h1>
                        <p style="font-size: 15px"><strong>Delete it if you want it gone for good. It cannot be undone !!</strong></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button class="button" style="background:#d54e21; color: white;">
                            Delete ? Are you sure ??
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    
</style>

<script>
    export default {
        data() {
            return {

            }
        },
        methods: {
            deleteProject() {

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
                            
                        } else {
                            vm.$router.push({ path: `/projects/${projectID}?type=unauthorized` });
                        }
                        
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