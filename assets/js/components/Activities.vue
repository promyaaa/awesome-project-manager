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
                        <li class="timeline-item" v-for="(value, key, index) in activitiesObject">
                            <div>
                                <h3>{{ key }}</h3>
                            </div>
                            <div class="timeline-marker"></div>
                            <div class="timeline-content" v-for="activity in value">
                                <div class="row" v-if="index % 2===0" style="margin-bottom: 10px;">
                                    <div class="col-2">{{activity.formatted_time}}</div>
                                    <div class="col-10">
                                        <activity-info :activity="activity" :i18n="i18n"></activity-info>
                                    </div>
                                </div>
                                <div class="row" v-else style="margin-bottom: 10px;">
                                    <div class="col-10 text-left">
                                        <activity-info :activity="activity"></activity-info>
                                    </div>
                                    <div class="col-2">{{activity.formatted_time}}</div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <!-- <div class="row" v-for="(activity, index) in activities">
                        <div class="col-2">
                            <span>{{activity.formatted_date}}</span>
                        </div>
                        <div class="col-2">
                            <span>{{activity.formatted_time}}</span>
                        </div>
                        <div class="col-8">
                            <activity-info :activity="activity" :i18n="i18n"></activity-info>
                        </div>
                    </div> -->
                    
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

        props: [
            'i18n'
        ],

        data () {
            return {
                activities: [],
                totalActivityCount: '',
                noActivity: false,
                activitiesObject : {},
                currentCount: ''
            }
        },

        methods: {
            fetchActivities() {
                var vm = this,
                    data,
                    activities = [],
                    current,
                    next,
                    i,
                    length,
                    keys;

                data = {
                    action: 'fpm-get-activities',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if (resp.success) {
                        length = resp.data.length;
                        vm.currentCount = length;
                        vm.totalActivityCount = resp.data[0].total_activity;
                        for(i = 0; i < length; i++ ) {

                            current = resp.data[i],
                            next = resp.data[i+1]; 
                            if(!next) {
                                activities = [];
                                if(resp.data[i].formatted_date === resp.data[i-1].formatted_date) {
                                    keys = Object.keys(vm.activitiesObject);
                                    vm.activitiesObject[keys[keys.length-1]].push(resp.data[i]);
                                } else {
                                    activities.push(resp.data[i]);
                                    vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                }
                                continue;
                            };

                            if(current.formatted_date === next.formatted_date) {
                                activities.push(resp.data[i]);
                                if((i+2) === length) {
                                    vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                    activities = [];
                                }
                            } else {
                                activities.push(resp.data[i]);
                                vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                activities = [];
                            }
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
                    },
                    activities = [],
                    keys = Object.keys(vm.activitiesObject),
                    previous = vm.activitiesObject[keys[keys.length-1]],
                    i,
                    current,
                    next,
                    length;

                vm.loadMore = true;

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loadMore = false;
                    
                    if (resp.success) {
                        length = resp.data.length;
                        vm.currentCount += length;

                        for(i = 0; i < length; i++ ) {

                            current = resp.data[i];

                            if(previous[0].formatted_date === current.formatted_date) {
                                previous.push(current);
                                continue;
                            }

                            next = resp.data[i+1]; 
                            if(!next) {
                                activities = [];
                                if(resp.data[i].formatted_date === resp.data[i-1].formatted_date) {
                                    keys = Object.keys(vm.activitiesObject);
                                    vm.activitiesObject[keys[keys.length-1]].push(resp.data[i]);
                                } else {
                                    activities.push(resp.data[i]);
                                    vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                }
                                continue;
                            };

                            if(current.formatted_date === next.formatted_date) {
                                activities.push(resp.data[i]);
                                if((i+2) === length) {
                                    vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                    activities = [];
                                }
                            } else {
                                activities.push(resp.data[i]);
                                vm.$set(vm.activitiesObject, resp.data[i].formatted_date, activities);
                                activities = [];
                            }
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