<template>
    <div class="container">
        <div class="lists">
            <div class="row">
                <div class="col-2"></div>
                <div class="col-8">
                    <div class="text-center assignment-heading">
                        <h1>My Bookmarks</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <div v-for="(value, key, index) in sortedResult">
                        <div>
                            <h3>{{key}}</h3>
                        </div>
                        <div v-for="bookmark in value" class="animated fadeIn">
                            <div v-if="bookmark.bookmark_type === 'project'">
                                <div class="bookmark-item">
                                    <span class="bookmark-icon">
                                        <i class="fa fa-briefcase"></i>
                                    </span>
                                    <router-link :to="'/projects/' + bookmark.bookmark_id">
                                        {{bookmark.bookmark_title}}
                                    </router-link>
                                </div> 
                            </div>
                            <div v-if="bookmark.bookmark_type === 'list'">
                                <div class="bookmark-item">
                                    <span class="bookmark-icon"><i class="fa fa-list"></i></span>
                                    <router-link :to="'/projects/'+bookmark.projectID+'/todolists/'+bookmark.bookmark_id">
                                        {{bookmark.bookmark_title}}
                                    </router-link>
                                </div> 
                            </div>
                            <div v-if="bookmark.bookmark_type === 'todo'">
                                <div class="bookmark-item">
                                    <span class="bookmark-icon"><i class="fa fa-check"></i></span>
                                    <router-link :to="'/projects/'+bookmark.projectID+'/todolists/'+bookmark.parentID+'/todos/'+bookmark.bookmark_id">
                                        {{bookmark.bookmark_title}}
                                    </router-link>
                                </div> 
                            </div>
                            <div v-if="bookmark.bookmark_type === 'message'">
                                <div class="bookmark-item">
                                    <span class="bookmark-icon">
                                        <i class="fa fa-envelope-o"></i>
                                    </span>
                                    <router-link :to="'/projects/'+bookmark.projectID+'/messages/'+bookmark.bookmark_id">
                                        {{bookmark.bookmark_title}}
                                    </router-link>
                                </div> 
                            </div>
                            <div v-if="bookmark.bookmark_type === 'folder'">
                                <div class="bookmark-item">
                                    <span class="bookmark-icon">
                                        <i class="fa fa-folder-o"></i>
                                    </span>
                                    <router-link :to="'/projects/'+bookmark.projectID+'/folders/'+bookmark.bookmark_id">
                                        {{bookmark.bookmark_title}}
                                    </router-link>
                                </div> 
                            </div>
                            <div v-if="bookmark.bookmark_type === 'file'">
                                <div class="bookmark-item">
                                    <span class="bookmark-icon">
                                        <i class="fa fa-file-o"></i>
                                    </span>
                                    <router-link :to="'/projects/'+bookmark.projectID+'/folders/'+bookmark.parentID+'/files/'+bookmark.bookmark_id">
                                        {{bookmark.bookmark_title}}
                                    </router-link>
                                </div> 
                            </div>
                            <div v-if="bookmark.bookmark_type === 'event'">
                                <div class="bookmark-item">
                                    <span class="bookmark-icon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <router-link :to="'/projects/'+bookmark.projectID+'/events/'+bookmark.bookmark_id">
                                        {{bookmark.bookmark_title}}
                                    </router-link>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="text-center" 
                        style="margin-top:15px" 
                        v-if="bookmarks.length < totalBookmark">
                        <button class="button button-default" @click="fetchBookmarks(true)">
                            Load More
                        </button>
                    </div>
                    <div v-if="bookmarks.length < 1 && !loading" class="text-center">
                        <p><strong>Nothing yet ;)</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        props: [],
        components: {

        },
        data() {
            return {
                bookmarks: [],
                totalBookmark: '',
                loading: false
            }
        },
        computed: {
            sortedResult() {
                return _.groupBy( this.bookmarks, 'project_title' )
            }
        },
        methods: {
            fetchBookmarks( isLoadMore ) {
                var vm = this,
                    data;
                data = {
                    action: 'fpm-get-bookmarks',
                    nonce: fpm.nonce,
                };
                vm.loading = true;
                if ( isLoadMore ) {
                    data.offset = vm.bookmarks.length;
                }
                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    if ( resp.success ) {
                        if ( !isLoadMore ) {
                            vm.totalBookmark = resp.data[0].total_bookmark;
                        }
                        for( var i = 0; i < resp.data.length; i++ ) {
                            vm.bookmarks.push(resp.data[i])
                        }
                    }
                });
            }
        },
        created() {
            this.fetchBookmarks()
        }
    }
</script>

<style>
    .bookmark-icon {
        padding: 5px 7px;
        border-radius: 40px;
        color: white;
        background: #267cb5;
        border: 1px solid #267cb5;
    }
    .bookmark-item {
        padding: 15px
    }
    .bookmark-item a {
        padding-left: 15px;
        text-decoration: underline;
    }
</style>