<template>
    <div class="form-group row px-3">
        <div v-if="type==='image'">
            <label :for="'item'+schema.name" class="w-100">
                <!--<i v-if="schema.description" class="hotbox-icon hotbox-icon-c-question" :title="schema.title" v-b-tooltip.hover="schema.description"></i>-->
                {{ schema.title }}
                <a v-if="type=='file' && value" class="small" :href="value" target="_blank">Download</a>
                <span v-if="isRequired" class="show-red small"> *(Required)</span>
            </label>
            <div style="position: relative; max-width: 175px;">
                <img v-if="type=='image'" :src="value || '/images/none.jpg'" class="img-responsive" :style="{width: !value ? '175px' : '175px', maxWidth: !value ? '175px' : '175px'}" />
                <i v-else class="fas fa-file-pdf fa-3x"></i>
                <spinner :isProcessing="isUploading" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="img-right-set float-right" @click="val=null" />
                <i v-if="val" class="img-right-set ti-close show-red" style="cursor: pointer;" @click="val=null"></i>
            </div>
            <div class="mt-2">
                <input type="file"
                       class="file-input"
                       :accept="acceptFileTypes.join(',')"
                       style="display:none"
                       :name="type"
                       :class="{'input': true, 'val-danger-input': errors.has(type) }"
                       :placeholder="schema.placeholder"
                       v-validate="schema.validation"
                       @change="onFileChange"
                       ref="fileInput">
                <button class="btn btn-sm" :class="{'btn-light':!file,'btn-default':file}" @click.prevent="$refs.fileInput.click()">
                    <span v-if="!value">{{ buttonLabelNew }}</span>
                    <span v-else>{{ buttonLabelReplace }}</span>
                </button>
                <span v-show="errors.has(type)" class="form-text text-muted val-danger-text">{{ errors.first(type) }}</span>
                <b-progress v-show="uploadProgress" :value="uploadProgress" :max="100" class="" height="1px" animated></b-progress>
            </div>
        </div>


        <div v-if="type==='file'">
            <label :for="'item'+schema.name" class="w-100">
                {{ schema.title }}
                <span v-if="isRequired" class="show-red small"> *(Required)</span>
            </label>

            <div v-if="isUploading">
                <spinner :isProcessing="isUploading" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" />
            </div>

            <div v-if="val && !isUploading" v-for="(file, index) in fileArray" :key="file.url">
                <div class="float-left" style="display:inline;">
                    <i class="ti-close show-red" style="font-size: smaller; margin-right: 20px; cursor: pointer;" :style="{cursor: isUploading || disabled ? 'inherit' : 'pointer'}" @click="confirmDelete(index)"></i>
                </div>

                <div style="display: inline;">
                    <a class="small" :href="file.url" target="_blank" style="color: unset;">{{ file.name }}</a>
                    <a class="small" :href="file.url" target="_blank" style="color: unset;"><icon-base :icon-name="file.name" height="16" width="16" ><component :is="'icon-' + file.type"></component>></icon-base></a>
                </div>
            </div>

            <div class="col-12" v-if="!isUploading">
                <input type="file"
                       class="file-input"
                       :accept="acceptFileTypes.join(',')"
                       style="display:none"
                       :name="type"
                       :class="{'input': true, 'val-danger-input': errors.has(type) }"
                       :placeholder="schema.placeholder"
                       v-validate="schema.validation"
                       @change="onFileChange"
                       ref="fileInput">
                <button class="btn btn-sm" :disabled="disabled" :class="{'btn-light':!file,'btn-default':file}" @click.prevent="$refs.fileInput.click()">
                    <span v-if="!value || multiple">{{ buttonLabelNew }}</span>
                    <span v-else>{{ buttonLabelReplace }}</span>
                </button>
                <span v-show="errors.has(type)" class="form-text text-muted val-danger-text">{{ errors.first(type) }}</span>
                <b-progress v-show="uploadProgress" :value="uploadProgress" :max="100" class="" height="1px" animated></b-progress>
            </div>
        </div>
    </div>
</template>

<script>
    import IconBase from "./IconBase";
    import IconPdf from "../icons/IconFilePdf"
    import IconDocument from "../icons/IconFileWordBrand"
    import IconSpreadsheet from "../icons/IconFileExcelBrand"
    import IconImage from "../icons/IconImage"
    import IconOther from "../icons/IconAttachment"

    export default {
        components: {
            IconBase,
            IconPdf,
            IconDocument,
            IconSpreadsheet,
            IconImage,
            IconOther
        },

        props: {
            value: {
                type: String,
                default: '',
            },
            schema: {
              type: Object,
              default: () => {}
            },
            type: {
                type: String,
                default: 'image'
            },
            resource: {
              type: String,
              default: ''              
            },
            view:{
                type: String,
                default: 'profile'
            },
            buttonLabelNew: {
                type: String,
                default: 'Upload File'
            },
            buttonLabelReplace: {
                type: String,
                default: 'Replace File'
            },
            disabled: {
                type: Boolean,
                default: false,
            },
            multiple: {
                type: Boolean, //multiple file uploads in this field.  Stored semicolon as delimiter (semicolons are removed from filenames as precaution)
                default: false,
            },
        },
        
        data(){
            return {
                file: null,
                isUploading: false,
                uploadProgress:0,
                accept: ['.doc','.docx','.pdf','.png','.jpg','.jpeg','.bmp','.gif','.tif','.tiff','.txt'], //enforced in ApiUploadAssetRequest.php
                acceptImage: ['.png','.jpg','.jpeg','.bmp','.gif'],
                maxFileSize: 1024*1024*5, //enforced in ApiUploadAssetRequest.php
            };
        },
        
    	computed: {
            acceptFileTypes() {
                if (this.type==='image') return this.acceptImage;
                return this.accept;
            },
            allowedFileTypes() {
                return this.acceptFileTypes.join(',').replace(/\./g,'');
            },
    		val: {
    			get() { return this.value; },
    			set(v) { this.$emit("input", v); }
    		},
            fileArray() {
    		    return (this.value || '').split(';').map(e => {
                    let a = e.split('/');
                    let filename = a[a.length-1];
                    let fileExtension = this.fileExtension(filename);

    		        return {
    		            url: e,
    		            name: filename,
                        extension: fileExtension,
                        type: this.fileType(fileExtension),
                    }
                });
            },
    		isRequired() {
    		    if(this.schema.validation)
    		        return (this.schema.validation.split("|").find(x => x == 'required')) ? true : false;
    		    return false;
    		},
    	},
    	
    	methods: {
            fileType(fileExtension) {
                switch (fileExtension) {
                    case 'doc':
                    case 'docx':
                    case 'docb':
                        return 'document';
                    case 'pdf':
                        return 'pdf';
                    case 'xls':
                    case 'xlsx':
                        return 'spreadsheet';
                    case 'png':
                    case 'gif':
                    case 'jpg':
                    case 'jpeg':
                    case 'tiff':
                    case 'bmp':
                        return 'image';

                }
                return 'other';
            },

            fileExtension(filename) {
                const a = filename.split('.');
                return a[a.length-1].toLowerCase();
            },

    	    onFileChange(e){
    	        this.file = e.target.files[0];                                 // this component handles 1 upload, which is first in files formdata array
    	        this.uploadFile();
    	    },

            readableFileSize(size) {
                const i = Math.floor( Math.log(size) / Math.log(1024) );
                return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
            },

    	    uploadFile(){
                if (this.file.size>this.maxFileSize) {
                    this.$announcer({status:400,data:{message:'File Too Big (Maximum ' + this.readableFileSize(this.maxFileSize) +')'}});
                    return false;
                }
                if (!this.accept.includes('.' + this.fileExtension(this.file.name))) {
                    this.$announcer({status:400,data:{message:'Invalid File Type (allowed types: '+this.allowedFileTypes+')'}});
                    return false;
                }
    	        const fd = new FormData();                                      // parse form and upload data
    	        fd.append(this.type,this.file,this.file.name);
                this.isUploading = true;   
                axios.post('/api/v1/admin/asset/'+this.resource.toLowerCase()+'/'+this.type,fd,{
                    onUploadProgress: ue => { this.uploadProgress = Math.round(ue.loaded / ue.total * 100); }
                }).then(response =>  {
                    this.isUploading = false;
                    console.log('multiple:',this.multiple ? 'true' : 'false');
                    if (this.multiple) {
                        this.val = this.val ? this.val + ';' + response.data.uri : response.data.uri;
                    } else {
                        this.val = response.data.uri;
                    }
                    this.file=null;
                    this.uploadProgress=0;
                }).catch(error => {
                    //debugger;
                    this.isUploading = false;
                    //this.file=null;
                    this.uploadProgress=0;
                    //check for invalid file/mime type
                    if (((((error||{}).response||{}).data||{}).errors||{}).file) { //error.response.data.errors.file[0]
                        switch (error.response.data.errors.file[0]) {
                            case 'validation.mimes':
                                error.response.data.errors.file[0]='Invalid File Type (allowed types: '+this.allowedFileTypes+')';
                                break;
                            case 'validation.max.file':
                                error.response.data.errors.file[0]='File Size Too Big (Maximum ' + this.readableFileSize(this.maxFileSize) +')';
                                break;
                        }
                    }
                    this.$announcer(error.response);
                });
    	    },

            confirmDelete(index){
    	        if (this.disabled) return;

                this.$swal.fire({
                    title: 'Are you sure?',
                    text: 'Delete ' + this.fileArray[index].name.toUpperCase(),
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete'
                }).then((result) => {
                    if(result.value){
                        this.removeFile(index);
                    }
                });
            },

            removeFile(index) {
                let a = this.fileArray;
                a.splice(index,1);
                this.val = a.map(e => {
                    return e.url;
                }).join(';');
            },
    	},
    	
    	inject: ['$validator']
    };
</script>

<style>
</style>