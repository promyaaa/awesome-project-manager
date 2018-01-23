<template>
    <!-- <div class="container"> -->
        <div class="row">
            <div class="col-12 text-center">
                <div class="activity-content">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="decorated-center"><span>Project Activity</span></h2>
                        </div>
                    </div>
                    <ul class="timeline timeline-centered">
                        <li class="timeline-item" v-for="(record, index) in activities">
                            <div>
                                <h3>{{record.activity_date}}</h3>
                            </div>
                            <div class="timeline-marker"></div>
                            <div class="timeline-content" v-for="activity in record.activities">
                                <!-- <pre>
                                    {{activity}}
                                </pre> -->
                                <div class="row" v-if="index % 2===0">
                                    <div class="col-2">{{activity.formatted_time}}</div>
                                    <div class="col-10">{{activity.activity}}</div>
                                </div>
                                <div class="row" v-else>
                                    <div class="col-10">{{activity.activity}}</div>
                                    <div class="col-2">{{activity.formatted_time}}</div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>  
            </div>
        </div>
    <!-- </div> -->
</template>

<style>
.activity-content {
    padding: 35px 20px; 
    /*border: 1px solid #e5e5e5;*/
    /*-webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.04);*/
    /*box-shadow: 0 1px 1px rgba(0,0,0,0.04);*/
    background: #fff;
}    
</style>

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
                    project_id: vm.$route.params.projectid,
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