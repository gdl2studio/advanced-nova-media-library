<template>
    <div v-if="field.type === 'media'" class="gallery">
        <template v-if="imagesUrls.length">
            <img v-for="url in imagesUrls" :src="url" style="object-fit: cover;" class="rounded w-8 h-8">

            <tooltip v-if="hasMore" trigger="click">
                <span class="more rounded w-8 h-8 cursor-pointer" :title="__('View')">&hellip;</span>

                <tooltip-content slot="content">
                    <div class="gallery">
                        <img v-for="url in allImagesUrls" :src="url" style="object-fit: cover;" class="rounded w-8 h-8">
                      </div>
                </tooltip-content>
            </tooltip>

        </template>
        <span v-else class="w-8 h-8">&mdash;</span>
    </div>
    <div v-else>
        <span v-if="field.multiple">
          {{ field.value.map(({ file_name }) => file_name).join(', ') }}
        </span>
        <span v-else>{{ field.value[0].file_name }}</span>
    </div>
</template>

<script>
	export default {
		props: ['resourceName', 'field'],

		computed: {
			value() {
				return this.field.value[0];
			},

			imageUrl() {
				return this.value.__media_urls__.indexView;
			},

			allImagesUrls() {
				return this.field.value.map(({__media_urls__}) => __media_urls__.indexView);
			},

			imagesUrls() {
				return this.hasMore ? this.allImagesUrls.slice(0, this.field.imagesLimit - 1) : this.allImagesUrls;
			},

			hasMore() {
				return 0 < this.field.imagesLimit && this.field.imagesLimit < this.field.value.length;
			}
		},
	};
</script>

<style scoped>
    .gallery {
        margin: 3px -10px -10px 0;
        display: flex;
        flex-wrap: wrap;
    }

    .gallery img, .gallery span {
        margin: 0 10px 10px 0;
    }

    .gallery span {
        text-align: center;
        display: inline-block;
        line-height: 32px;
    }

    .more {
        background-color: var(--50);
    }
</style>