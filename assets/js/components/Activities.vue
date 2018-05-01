<template>
    <!-- <div class="container"> -->
        <div class="row">
            <div class="col-12 text-center">
                <div class="activity-content">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="decorated-center"><span>{{ i18n.project_activity }}</span></h2>
                        </div>
                    </div>
                    
                    <ul class="timeline timeline-centered">
                        <li class="timeline-item animated fadeIn" v-for="(value, key, index) in activitiesObject">
                            <div>
                                <h3>{{ key }}</h3>
                            </div>
                            <div class="timeline-marker"></div>
                            <div class="timeline-content animated fadeIn" v-for="activity in value">
                                <div class="row" v-if="index % 2===0" style="margin-bottom: 10px;">
                                    <div class="col-3">{{activity.formatted_time}}</div>
                                    <div class="col-9">
                                        <activity-info :activity="activity" :i18n="i18n"></activity-info>
                                    </div>
                                </div>
                                <div class="row" v-else style="margin-bottom: 10px;">
                                    <div class="col-9 text-left">
                                        <activity-info :activity="activity"></activity-info>
                                    </div>
                                    <div class="col-3">{{activity.formatted_time}}</div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    
                    <div class="row" v-if="currentCount < totalActivityCount">
                        <div class="col-12">
                            <button class="button" @click="loadMoreActivities">Load More</button>
                        </div>
                    </div>
                    <div v-if="noActivity">
                        {{ i18n.no_activity_yet }}
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

        data () {
            return {
                activities: [],
                totalActivityCount: '',
                currentCount: ''
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

                data = {
                    action: 'fpm-get-activities',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };
                if ( userid ) {
                    data.user_id = userid;
                }

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if(resp.success) {
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
            this.fetchActivities();
        }
    }
</script>