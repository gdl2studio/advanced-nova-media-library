<template>
    <gallery-item class="gallery-item-image" :class="{ 'show-dimensions': field.showDimensions }">
        <div class="gallery-item-info p-2">
            <a v-if="isCustomPropertiesEditable" class="icon edit" href="#"
               @click.prevent="$emit('edit-custom-properties')" :title="__('Edit custom properties')">
                <icon type="edit" view-box="0 0 20 20" width="16" height="16"/>
            </a>
            <a v-if="downloadable && downloadUrl" class="icon download" :href="downloadUrl" :title="__('Download')">
                <icon type="download" view-box="0 0 20 22" width="16" height="16"/>
            </a>
            <a v-if="removable" class="icon delete" href="#" @click.prevent="$emit('remove')" :title="__('Remove')">
                <icon type="delete" view-box="0 0 20 20" width="16" height="16"/>
            </a>
            <a class="preview" href="#" @click.prevent="showPreview">
                <icon type="search" view-box="0 0 20 20" width="30" height="30"/>
            </a>
            <a v-if="croppable" class="icon crop" href="#" @click.prevent="$emit('crop-start', image)">
                <scissors-icon brand="var(--info)" view-box="0 0 20 20" width="16" height="16"/>
            </a>
        </div>
        <img :src="src" :alt="image.name" ref="image" class="gallery-image">
        <div v-if="field.showDimensions || field.showRatio" class="dimensions">
            <div v-if="field.showDimensions"><strong>{{ width }}×{{ height }}</strong> px</div>
            <div v-if="field.showRatio"><strong>{{ acpectRatio }}</strong> ({{ratio}})</div>
        </div>
        <div v-if="field.showMimeType" class="type">
            {{ mimeType }}
        </div>
    </gallery-item>
</template>

<script>
	import ScissorsIcon from './icons/Scissors';
	import GalleryItem from './GalleryItem';

	export default {
		components: {
			ScissorsIcon,
			GalleryItem,
		},
		props: ['image', 'field', 'removable', 'editable', 'downloadable', 'isCustomPropertiesEditable'],
		data: () => ({
			acceptedMimeTypes: ['image/jpg', 'image/jpeg', 'image/png'],
			src: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==",
			width: undefined,
			height: undefined,
			acpectRatio: undefined,
			ratio: undefined,
		}),
		computed: {
			downloadUrl() {
				return this.image.id ? `/nova-vendor/ebess/advanced-nova-media-library/download/${this.image.id}` : null;
			},
			croppable() {
				return this.editable &&
					this.field.croppable &&
					this.acceptedMimeTypes.includes(this.mimeType);
			},
			mimeType() {
				return this.image.mime_type || this.image.file.type;
			},
		},
		watch: {
			image: {
				handler: 'getImage',
				immediate: true
			}
		},
		methods: {
			showPreview() {
				const blobUrl = this.image.file ? URL.createObjectURL(this.image.file) : this.image.__media_urls__.preview;
				window.open(blobUrl, '_blank');
			},
			getImage() {
				if (this.editable && this.image.__media_urls__.form) {
					this.src = this.image.__media_urls__.form;
				} else if (!this.editable && this.image.__media_urls__.detailView) {
					this.src = this.image.__media_urls__.detailView;
				} else if (this.isVideo(this.image.__media_urls__.__original__)) {
					//Seconds to seek to, to get thumbnail of video
					let seconds = 1;  //TODO get this from the field instead of hardcoding it here
					this.getVideoThumbnail(this.image.__media_urls__.__original__, seconds);
				} else {
					this.src = this.image.__media_urls__.__original__;
				}

				if (this.field.showDimensions || this.field.showRatio) {
					setTimeout(this.calculateDimensions);
				}
			},
			getVideoThumbnail(path, secs = 0) {
				const video = document.createElement('video');
				video.onloadedmetadata = () => {
					video.currentTime = Math.min(Math.max(0, (secs < 0 ? video.duration : 0) + secs), video.duration);
				};
				video.onseeked = (e) => {
					const canvas = document.createElement('canvas');
					canvas.height = video.videoHeight;
					canvas.width = video.videoWidth;
					const ctx = canvas.getContext('2d');
					ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
					this.src = canvas.toDataURL();
				};
				video.src = path;
			},
			isVideo(mediaPath) {
				if (mediaPath.startsWith("data:video"))
					return true;
				//TODO better video detection
				const supportedExtensions = [".mp4", ".ogv", ".webm"];
				return supportedExtensions.some((suffix) => {
					return mediaPath.endsWith(suffix)
				});
			},
			calculateDimensions() {
				if (this.$refs.image.complete) {
					this.width = this.$refs.image.naturalWidth;
					this.height = this.$refs.image.naturalHeight;
					this.ratio = Math.round((this.width / this.height) * 100) / 100;

					const gcd = this.gcd(this.width, this.height);
					this.acpectRatio = (this.width / gcd) + ':' + (this.height / gcd);
				} else {
					this.$refs.image.onload = this.calculateDimensions;
				}
			},
			gcd(a, b) {
				if (b === 0) {
					return a;
				}

				return this.gcd(b, a % b);
			}
		},
	};
</script>

<style lang="scss">
    $bg-color: #e8f5fb;
    $item-max-size: 150px;
    $border-radius: 5px;

    .gallery {
        .gallery-item-image.gallery-item {
            width: $item-max-size;
            height: $item-max-size;

            &:hover .gallery-item-info {
                display: flex;
            }

            /*&.show-dimensions {*/
            /*  padding-bottom: 24px;*/
            /*}*/

            .gallery-item-info {
                display: none;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                background-color: transparentize($bg-color, .2);
                border-radius: $border-radius;
                position: absolute;
                z-index: 10;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;

                .preview {
                    color: var(--black);
                }

                .crop {
                    left: 10px;
                    top: auto;
                    bottom: 10px;
                }
            }

            .gallery-image {
                object-fit: contain;
                display: block;
                max-height: 100%;
                border-radius: $border-radius;
            }

            .dimensions, .type {
                position: absolute;
                left: 0;
                width: 100%;
                font-size: .75rem;
                line-height: 0.95;
                text-align: center;
                background-color: transparentize($bg-color, .2);
                border-radius: $border-radius;
                z-index: 5;
                padding: 4px;
            }


            .dimensions {
                bottom: 0;
                padding-right: 0;
            }

            .type {
                top: 0;
            }
        }

        .icon {
            cursor: pointer;
            position: absolute;
            top: 10px;
            color: var(--info);
        }

        .edit, .download {
            left: 10px;
        }

        .edit + .download {
            left: 30px !important;
        }

        .delete {
            right: 10px;
            color: var(--danger);
        }

    }
</style>
