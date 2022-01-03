
<template>
    <div class="mb-5">
        <div class="file-picker-container" :class="{'picker-error': error, 'has-file': isFileSelected}">
            <div class="file-picker-wrapper">
                <label :for="'file-input-'+id">
                    <span v-if="!fileName">{{ label || 'Select File' }}</span>
                    <span v-if="fileName">{{ fileName }}</span>
                </label>
                <div class="file-size">{{ fileSize }}</div>
                <input :id="'file-input-'+id" :accept="accept" :name="name || 'file'" type="file" @change='onFileChange'>
            </div>
        </div>
        <div class="file-picker-error" v-if="error">{{ error }}</div>
    </div>
</template>

<script>
export default {
    props: ['name', 'label', 'accept', 'error'],
    data() {
        return {
            isFileSelected: false,
            fileSizeWithoutUnit: 0,
            fileName: null,
            fileSize: null,
            id: null,
        }
    },
    mounted () {
        this.id = this._uid
    },
    methods: {
        onFileChange(event) {
            if (event.target.files.length) {
                const file = event.target.files[0]
                const { name, size } = file
                let _size = size
                this.fileName = name
                const fSExt = new Array('Bytes', 'KB', 'MB', 'GB')
                let i = 0
                while(_size > 900){_size/=1024;i++;}
                this.fileSizeWithoutUnit = (Math.round(_size *100)/100)
                this.fileSize = this.fileSizeWithoutUnit +' '+ fSExt[i];
                this.isFileSelected = true;
                this.$emit('change', file);
            }
        }
    }
}
</script>

<style lang="scss">
.file-picker-container {
    position: relative;
    border: 2px dashed #d2d2d2;
    padding: 15px 20px;
    cursor: pointer;
    border-radius: 10px;
    overflow: hidden;
    transition: border-color 0.2s ease-in-out;
    &:hover, &.has-file {
        border-color: blue;
        label {
            color: blue;
        }
    }
    &.picker-error {
        border-color: red;
        label, .file-size {
            color: red !important;
        }
    }
    .file-picker-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        label {
            position: relative;
            z-index: 3;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            cursor: pointer;
        }
        .file-size {
            white-space: nowrap;
            font-size: 13px;
            color: green;
        }
        input[type=file] {
            display: none;
        }
    }
}
.file-picker-error {
    margin-top: 5px;
    font-size: 12px;
    color: red;
    padding: 0 10px;
}
</style>
