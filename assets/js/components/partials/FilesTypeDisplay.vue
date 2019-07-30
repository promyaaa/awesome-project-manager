<template>
    <div>
        <div v-if ="isSmallView">
            <div v-if="isImageFile">
                <img :src="file.url" width="90" height="90" class="display-small-image">
            </div>
            <div v-if="isTextFile">
                <img :src="file.icon" width="68" height="90" class="display-small-image">
            </div>
            <div v-if="isPdfFile">
                <img :src="file.icon" width="68" height="90" class="display-small-image">
            </div>
            <div v-if="isJavascriptFile">
                <img :src="file.icon" width="68" height="90" class="display-small-image">
            </div>
            <div v-if="isCompressedFile">
                <img :src="file.icon" width="68" height="90" class="display-small-image">
            </div>
            <div v-if="isDocumentFile">
                <img :src="file.icon" width="68" height="90" class="display-small-image">
            </div>
            <div v-if="isPresentationFile">
                <img :src="file.icon" width="68" height="90" class="display-small-image">
            </div>
        </div>
        <div v-if="isNormalView" class="text-center">
            <div v-if="isImageFile">
                <img :src="file.url" alt="" class="image-resize common-file-style">
                <div class="file-action">
                    {{file.title}}.{{file.extension}} - <a :href="file.url" target="_blank">View full-size</a> - <a :href="file.url" download>Download</a>
                </div>
            </div>
            <div v-if="isTextFile">
                <img :src="file.icon"  class="common-file-style">
                <div class="file-action">
                    {{file.title}}.{{file.extension}} - <a :href="file.url" download>Download</a>
                </div>
            </div>
            <div v-if="isPdfFile" >
                <img :src="file.icon"  class="common-file-style">
                <div class="file-action">
                    {{file.title}}.{{file.extension}} - <a :href="file.url" download>Download</a>
                </div>
            </div>
            <div v-if="isJavascriptFile">
                <img :src="file.icon"  class="common-file-style">
                <div class="file-action">
                    {{file.title}}.{{file.extension}} - <a :href="file.url" download>Download</a>
                </div>
            </div>
            <div v-if="isCompressedFile">
                <img :src="file.icon"  class="common-file-style">
                <div class="file-action">
                    {{file.title}}.{{file.extension}} - <a :href="file.url" download>Download</a>
                </div>
            </div>
            <div v-if="isDocumentFile">
                <img :src="file.icon"  class="common-file-style">
                <div class="file-action">
                    {{file.title}}.{{file.extension}} - <a :href="file.url" download>Download</a>
                </div>
            </div>
            <div v-if="isPresentationFile" >
                <img :src="file.icon"  class="common-file-style">
                <div class="file-action">
                    {{file.title}}.{{file.extension}} - <a :href="file.url" download>Download</a>
                </div>
            </div>
        </div>
        <div class="text-center" v-if="isFolderView">
            <div v-if="isImageFile">
                <img :src="file.url" alt="" class="image-resize common-file-style">
            </div>
            <div v-if="isTextFile">
                <img :src="file.icon" class="common-file-style">
            </div>
            <div v-if="isPdfFile" >
                <img :src="file.icon" class="common-file-style">
            </div>
            <div v-if="isJavascriptFile">
                <img :src="file.icon" class="common-file-style">
            </div>
            <div v-if="isCompressedFile">
                <img :src="file.icon" class="common-file-style">
            </div>
            <div v-if="isDocumentFile">
                <img :src="file.icon" class="common-file-style">
            </div>
            <div v-if="isPresentationFile">
                <img :src="file.icon" class="common-file-style">
            </div>
        </div>
    </div>
</template>

<style>
    .display-small-image {
        padding: 2px;
        text-align: center;
        box-sizing: border-box;
        border: 1px solid #e3e3e3;
        margin-bottom: 7px;
        display: block;
    }
    .common-file-style {
        padding: 3px;
        border: 1px solid #eee;
    }
    .file-action a {
        font-size: 13px;
        text-decoration: none;
    }
</style>

<script>
    export default {
        props: ['file', 'type'],
        computed: {
            isSmallView() {
                return this.type === 'small';
            },
            isNormalView() {
                return this.type === 'normal';
            },
            isFolderView() {
                return this.type === 'folder';
            },
            isImageFile() {
                var mime = this.file.mime;
                if( mime === 'image/gif' ) {
                    return true;
                } 
                if (mime === 'image/png') {
                    return true;
                }
                if (mime === 'image/jpeg') {
                    return true;
                }
                return false;
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