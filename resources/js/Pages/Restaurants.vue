<script>
import axios from 'axios';
export default {
  data() {
    return {
      keyword: 'Bang Sue',
      dataSource: [],
    }
  },
  mounted() {
    this.getListRestaurant();
  },
  methods: {
    getListRestaurant() {
      axios.get(`/api?keyword=${this.keyword}`)
        .then(res => {
          this.dataSource = res.data;
        })
    },
    openMapByPlaceID(place_id) {
      window.open(`https://www.google.com/maps/place/?q=place_id:${place_id}`, "_blank")
    }
  }
}
</script>

<template>
  <div class="container">
    <!-- Search Content -->
    <div class="row row-cols-lg-auto g-3 align-items-center mb-2">
      <div class="col-10">
        <label class="visually-hidden" for="inlineFormInputGroupUsername">Keyword</label>
        <div class="input-group">
          <input type="text" class="form-control" id="inlineFormInputGroupUsername"
          v-on:keyup.enter="getListRestaurant()"
           placeholder="Keyword"
            v-model="keyword">
        </div>
      </div>
      <div class="col-2">
        <button type="button" class="btn btn-primary" 
        v-on:keyup.enter="getListRestaurant()"
        @click="getListRestaurant()">Search</button>
      </div>
    </div>
    <!-- End Search Content -->

    <!-- LIST Content -->
    <div class="list-group" v-if="this.dataSource.results?.length > 0">
      <template v-for="item in this.dataSource.results">
        <a href="#" class="list-group-item list-group-item-action" @click="openMapByPlaceID(item.place_id)">
          <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">{{ item.name }}</h5>
            <small class="text-muted">{{ item.rating }} <i class="bi bi-star"></i></small>
          </div>
          <p class="mb-1">{{ item.vicinity }}</p>
        </a>
      </template>

    </div>
    <div class="list-group" v-else>
      <a href="#" class="list-group-item list-group-item-action list-group-item-secondary text-center">No Data Found</a>
    </div>
    <!-- End List Content -->
  </div>
</template>