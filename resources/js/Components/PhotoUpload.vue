<template>
  <div class="photo-upload">
    <input type="file" @change="onFileChange" accept="image/*" multiple />

    <div v-if="previewUrls.length" class="image-preview">
      <div v-for="(url, index) in previewUrls" :key="index" class="image-container">
        <img :src="url" alt="Preview" />
        <button @click="removeImage(index)">X</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeUnmount, defineEmits } from 'vue';

const emit = defineEmits();
const files = ref([]);
const previewUrls = ref([]);

const onFileChange = (event) => {
  const selectedFiles = Array.from(event.target.files);

  selectedFiles.forEach((file) => {
    files.value.push(file);
    const url = URL.createObjectURL(file);
    previewUrls.value.push(url);

    emit('update:files', files.value);
  });
};

const removeImage = (index) => {
  URL.revokeObjectURL(previewUrls.value[index]);
  previewUrls.value.splice(index, 1);
  files.value.splice(index, 1);

  emit('update:files', files.value);
};

onBeforeUnmount(() => {
  previewUrls.value.forEach((url) => URL.revokeObjectURL(url));
});
</script>

<style scoped>
.photo-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.image-preview {
  margin-top: 20px;
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
}

.image-container {
  position: relative;
}

.image-container img {
  max-width: 100%;
  max-height: 150px;
}

.image-container button {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: red;
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
}
</style>
