<template>
    <div class="container lists">
        <div class="row">
            <div class="col-12 text-center">
                <div class="activity-content">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="decorated-center"><span>My Activity</span></h2>
                        </div>
                    </div>
                    
                    <ul>
                        <li class="left" v-for="(value, key, index) in activitiesObject">
                            <h3>{{ key }}</h3>
                            <div class="animated fadeIn" v-for="activity in value">
                                <activity-info :activity="activity" :i18n="i18n"></activity-info>
                            </div>
                        </li>
                    </ul>
                    
                    <div class="row" v-if="currentCount < totalActivityCount">
                        <div class="col-12">
                            <button class="button" 
                                    @click="loadMoreActivities"
                                    :disabled="loadMore">
                                <i v-if="loadMore" class="fa fa-refresh fa-spin"></i>
                                Load More
                            </button>
                        </div>
                    </div>
                    <div v-if="noActivity && !loading">
                        No activity yet
                    </div>
                    <div v-if="loading">
                        Loading! Please wait... <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>

<style>
.activity-content {
    padding: 35px 20px;
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

        props: ['i18n'],

        data() {
            return {
                activities: [],
                totalActivityCount: '',
                currentCount: '',
                loading: false,
                loadMore: false,
            }
        },

        computed: {
            noActivity() {
                return this.totalActivityCount < 1;
            },
            activitiesObject() {
                return _.groupBy( this.activities, 'formatted_date' )
            }
        },

        methods: {
            fetchActivities( userid ) {
                var vm = this,
                    data;

                vm.loading = true;

                data = {
                    action: 'fpm-get-activities',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };
                if ( userid ) {
                    data.user_id = userid;
                }

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.loading = false;
                        for(var i = 0; i < resp.data.length; i++) {
                            vm.currentCount = resp.data.length;
                            vm.activities.push(resp.data[i]);
                            vm.totalActivityCount = resp.data[0].total_activity;
                        }
                    }
                });
            },
            loadMoreActivities() {
                var vm = this,
                    data = {
                        action: 'fpm-load-more-activities',
                        nonce: fpm.nonce,
                        offset: vm.currentCount,
                        project_id: vm.$route.params.projectid
                    };

                vm.loadMore = true;

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loadMore = false;

                    if(resp.success) {
                        for(var i = 0; i < resp.data.length; i++) {
                            vm.currentCount += resp.data.length;
                            vm.activities.push(resp.data[i]);
                        }
                    }
                   
                });
            },
        },

        created() {
            var userid = fpm.currentUserInfo.ID
            this.fetchActivities(userid);
        }
    }
</script>