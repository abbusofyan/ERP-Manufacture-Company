<template>
  <div class="duration-input">
    <input
      type="number"
      v-model="hours"
      min="0"
      placeholder="0"
      class="input-hours"
    />
    <span>h</span>
    <input
      type="number"
      v-model="minutes"
      min="0"
      max="59"
      placeholder="0"
      class="input-minutes"
    />
    <span>m</span>
  </div>
</template>

<script>
export default {
  name: 'DurationInput',
  data() {
    return {
      hours: 0,
      minutes: 0
    };
  },
  watch: {
    hours(val) {
      this.formatInput();
    },
    minutes(val) {
      if (val >= 60) {
        this.hours += Math.floor(val / 60);
        this.minutes = val % 60;
      }
      this.formatInput();
    }
  },
  methods: {
    formatInput() {
      // Emit the formatted duration as "3h:10m" or "12h:15m"
      this.$emit('update:duration', `${this.hours}h:${this.minutes}m`);
    }
  }
};
</script>

<style scoped>
.duration-input {
  display: flex;
  align-items: center;
}

.input-hours,
.input-minutes {
  width: 3rem;
  text-align: center;
}

span {
  margin: 0 0.2rem;
}
</style>
