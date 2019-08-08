<template>
    <div class="container">
        <project-nav></project-nav>
        <div class="lists border-for-nav">
            <activity></activity>    
        </div>
    </div>
</template>

<script>
    import store from '../store';
    import ActivityInfo from './partials/ActivityInfo.vue';
    import ProjectNav from './partials/ProjectNavComponent.vue';
    import Activity from './Activities.vue';
    export default {
        components: {
            ActivityInfo,
            ProjectNav,
            Activity
        },

        data () {
            return {
                activities: [],
                totalActivityCount: '',
                currentCount: '',
                i18n: {},
                project: {},
                activityTitle: ''
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
            fetchProjectInfo: function() {
                var vm = this;

                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                    } else {
                        vm.$router.push({
                            path: `/?item=Project&op=rf`
                        });
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
            var vm = this;
            // store.setLocalization( 'fpm-get-activities-local-data' ).then( function( data ) {
            //     vm.i18n = data;
            // });

            if ( vm.$route.params.userid ) {
                vm.fetchActivities( vm.$route.params.userid );

            } else {
                vm.fetchActivities();
                vm.activityTitle = 'Project Activity';
            }
            
            vm.fetchProjectInfo();
        }
    }
</script>

<style>
.activity-content {
    /*padding: 20px;*/
    background: #fff;
}
.activity-avatar {
    float: left;
    margin-right: 10px;
    margin-top: 5px;
}

</style>