
<template>
    <div class="image-preview-wrap" :class="{'avatar': type == 'avatar' || type == 'avatar-card', 'has-error': error, 'cover': type == 'cover'}">
        <div class="form-group cover-file-upload col-sm-12" :class="{'active': isFileSelected, 'card-avatar': type == 'avatar-card'}">
            <label :for="'fileImage-'+componentId">{{ label || 'Select Image' }}</label>
            <input :id="'fileImage-'+componentId" :name="name || 'image'" accept="image/*" type="file" @change='onFileChange'>
            <img v-if="oldVlaue" class="file-image" :src="oldVlaue">
        </div>
    </div>
</template>

<script>
export default {
    name: 'imagePreview',
    props: ['value', 'name', 'type', 'error', 'state', 'label'],
    data() {
        return {
            isFileSelected: false,
            oldVlaue: null,
            componentId: null
        }
    },
    mounted: function() {
        this.componentId = this._uid
        if (this.value && this.value.indexOf('/storage') > -1)
            this.oldVlaue = this.value;
    },
    watch: { 
      	state: function(newVal, oldVal) { // watch it
            if (newVal) {
                this.oldValue = null;
                document.getElementById('fileImage').value = "";
                this.isFileSelected = false;
            }
        }
    },
    methods: {
        onFileChange(event) {
            if (event.target.files.length) {
                let url = URL.createObjectURL(event.target.files[0]);
                if (url) {
                    this.oldVlaue = url;
                    this.isFileSelected = true;
                    this.$emit('change', event.target.files[0]);
                }
            }
        }
    }
}
</script>

<style lang="scss">
.image-preview-wrap {
    text-align: center;
    &.has-error {
        .cover-file-upload {
            border: 2px dashed red;
            color: red;
        }
    }
    &.avatar {
        .cover-file-upload {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: inline-block;
            background: #fff;
        }
    }
    &.cover {
        .cover-file-upload {
            display: inline-block;
            width: 150px;
        }
    }
    .cover-file-upload {
        position: relative;
        border: 2px dashed #d2d2d2;
        height: 200px;
        cursor: pointer;
        border-radius: 10px;
        overflow: hidden;
        label {
            position: relative;
            z-index: 3;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        input[type=file] {
            display: none;
        }
        .file-image {
            position: absolute;
            z-index: 1;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        &.active {
            border: 3px solid #2579d0;
            label { opacity: 0; }
        }
    }
}
</style>
