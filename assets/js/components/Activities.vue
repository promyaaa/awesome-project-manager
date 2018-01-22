<template>
    <div>
        <div v-for="record in activities">
                <h3>{{record.activity_date}}</h3><br>
            <div v-for="activity in record.activities">
                {{activity.activity}}
            </div><br>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                activities: []
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
                        vm.activities = resp.data;
                    }
                });
            }
        },

        created() {
            this.fetchActivities();
        }
    }
</script>