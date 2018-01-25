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
                    <div class="row" v-for="(activity, index) in activities">
                        <div class="col-2">
                            <span>{{activity.formatted_date}}</span>
                        </div>
                        <div class="col-2">
                            <span>{{activity.formatted_time}}</span>
                        </div>
                        <div class="col-8">
                            <activity-info :activity="activity"></activity-info>
                        </div>
                    </div>
                    
                    <div class="row" v-if="activities.length < totalActivityCount">
                        <div class="col-12">
                            <button class="button" @click="loadMoreActivities">Load More</button>
                        </div>
                    </div>
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
.activity-avatar {
    float: left;
    margin-right: 10px;
    margin-top: 5px;
}
</style>

<script>
    import ActivityInfo from './partials/ActivityInfo.vue';
    export default {
        components: {
            ActivityInfo
        },

        data () {
            return {
                activities: [],
                totalActivityCount: ''
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
                        vm.totalActivityCount = resp.data[0].total_activity;
                    }
                });
            },

            loadMoreActivities() {
                var vm = this;
                vm.loadMore = true;
                var data = {
                    action: 'fpm-load-more-activities',
                    nonce: fpm.nonce,
                    offset: vm.activities.length,
                    project_id: vm.$route.params.projectid
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loadMore = false;
                    console.log(resp);
                    if ( resp.success ) {
                        vm.activityCount = vm.activityCount + resp.data[0].record_count;
                        for(var i = 0; i < resp.data.length; i++ ) {
                            vm.activities.push(resp.data[i]);
                        }
                    }
                });
            },
        },

        created() {
            this.fetchActivities();
        }
    }
</script>