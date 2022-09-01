import axios from "axios";

const baseURL = "http://localhost:8000/todos";

const instance = axios.create({
	baseURL
});

export default instance
