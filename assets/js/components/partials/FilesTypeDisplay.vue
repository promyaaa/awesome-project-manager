<template>
    <div>
        <div v-if="isSmallView">
            <div v-if="isImageFile">
                <strong>Image</strong>
                <div><i>{{file.title | truncate('11')}}</i></div>
                <img :src="file.url" width="90" height="90" style="display:block;" class="display-small-image">
            </div>
            <div v-if="isTextFile">
                <strong>Text</strong>
                <div><i>{{file.title | truncate('11')}}</i></div>
                <img :src="file.icon" width="68" height="90" style="display:block;" class="display-small-image">
            </div>
            <div v-if="isPdfFile">
                <strong>PDF</strong>
                <div><i>{{file.title | truncate('11')}}</i></div>
                <img :src="file.icon" width="68" height="90" style="display:block;" class="display-small-image">
            </div>
            <div v-if="isJavascriptFile">
                <strong>Js</strong>
                <div><i>{{file.title | truncate('11')}}</i></div>
                <img :src="file.icon" width="68" height="90" style="display:block;" class="display-small-image">
            </div>
            <div v-if="isCompressedFile">
                <strong>Compress</strong>
                <div><i>{{file.title | truncate('11')}}</i></div>
                <img :src="file.icon" width="68" height="90" style="display:block;" class="display-small-image">
            </div>
            <div v-if="isDocumentFile">
                <strong>Document</strong>
                <div><i>{{file.title | truncate('11')}}</i></div>
                <img :src="file.icon" width="68" height="90" style="display:block;" class="display-small-image">
            </div>
            <div v-if="isPresentationFile">
                <strong>Presentation</strong>
                <div><i>{{file.title | truncate('11')}}</i></div>
                <img :src="file.icon" width="68" height="90" style="display:block;" class="display-small-image">
            </div>
        </div>
        <div v-if="isNormalView">
            normal view
        </div>
        <!-- <pre>
            {{file}}
        </pre> -->
    </div>
</template>

<style>
    .display-small-image {
        padding: 5px;
        text-align: center;
        box-sizing: border-box;
    }
</style>

<script>
    export default {
        props: ['file', 'type'],
        filters: {
            truncate: function(string, value) {
                var dotdot = '';

                if (string.length > value) {
                    dotdot = '...';
                }
                return string.substring(0, value) + dotdot;
            }
        },
        computed: {
            isSmallView() {
                return this.type === 'small';
            },
            isNormalView() {
                return this.type === 'normal';
            },
            isImageFile() {
                return this.file.type === 'image';
            },
            isTextFile() {
                var mime = this.file.mime;
                if( mime === 'text/plain' ) {
                    return true;
                } 
                if (mime === 'text/csv') {
                    return true;
                }
                if (mime === 'text/css') {
                    return true;
                }
                if (mime === 'text/html') {
                    return true;
                }
                return false;
            },
            isPdfFile() {
                return this.file.mime === 'application/pdf';
            },
            isJavascriptFile() {
                return this.file.mime === 'application/javascript';
            },
            isCompressedFile() {
                var mime = this.file.mime;
                if( mime === 'application/x-7z-compressed' ) {
                    return true;
                } 
                if (mime === 'application/rar') {
                    return true;
                }
                if (mime === 'application/x-gzip') {
                    return true;
                }
                if (mime === 'application/zip') {
                    return true;
                }
                if (mime === 'application/x-tar') {
                    return true;
                }
                return false;
            },
            isDocumentFile() {
                var mime = this.file.mime;
                if( mime === 'application/vnd.oasis.opendocument.text' ) {
                    return true;
                } 
                if (mime === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    return true;
                }
                if (mime === 'application/msword') {
                    return true;
                }
                return false;
            },
            isPresentationFile() {
                var mime = this.file.mime;
                if( mime === 'application/vnd.oasis.opendocument.presentation' ) {
                    return true;
                } 
                if (mime === 'application/vnd.ms-powerpoint') {
                    return true;
                }
                return false;
            }
        }
    }
</script>