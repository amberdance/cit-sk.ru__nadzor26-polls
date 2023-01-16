import axios from "axios";

const validateData = async (responseData, payload) => {
  if (!responseData) return Promise.reject("Unhandled data");

  if (typeof responseData == "string")
    return Promise.reject("Cannot find condition to handle requested data");

  if (Object.keys(responseData).length == 1 && responseData.status)
    return payload;

  if (
    responseData instanceof Object &&
    "data" in responseData &&
    Object.keys(responseData.data).length == 1
  ) {
    return {
      ...payload,
      id: responseData.data.id || payload.id,
    };
  }

  if (responseData instanceof Object && "data" in responseData)
    return responseData.data;

  if (
    (Array.isArray(responseData) && responseData.length) ||
    responseData instanceof Object
  )
    return responseData;

  return Promise.reject("Cannot find condition to handle request data");
};

export const dispatch = {
  async HTTPPost({ route, payload }) {
    const { data } = await axios.post(route, payload);

    return validateData(data, payload);
  },

  async HTTPGet({ route, payload }) {
    const { data } = await axios.get(route, { params: payload });

    return validateData(data, payload);
  },
};
