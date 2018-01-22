<template>
    <div>
        
    </div>
</template>

<script>
    export default {
        data () {
            return {

            }
        },

        methods: {
            fetchActivities() {
                var vm = this;
                // vm.loading = true;
                
                var data = {
                    action: 'fpm-get-activities',
                    // project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // vm.loading = false;
                    console.log(resp);
                    if ( resp.success ) {
                        // vm.lists = resp.data;
                    }
                });
            }
        },

        created() {
            this.fetchActivities();
        }
    }
</script>