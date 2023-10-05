#How to build multi-arch docker image with Docker (DO NOT USE, PRODUCES BAD IMAGES, USE BUILDAH INSTEAD):

```sh
docker buildx build --push --platform linux/arm64/v8,linux/amd64 --tag registry.gitlab.com/xhibitsignage-v3/xhibitsignage-v3-backend:php-8.1.21 -f Dockerfile.production .
```

#How to build multi-arch docker image with Buildah:

##Run buildah in container from the folder where dockerfile located:

```sh
docker run -it --device /dev/fuse:rw --security-opt seccomp=unconfined --security-opt apparmor=unconfined -v $(pwd):/tmp/docker quay.io/buildah/stable:latest /bin/sh
```

##Build images

```sh
for i in arm64 amd64
do
    buildah bud --squash --manifest registry.gitlab.com/xhibitsignage-v3/xhibitsignage-v3-backend:php-8.1.21 --arch $i -f /tmp/docker/Dockerfile.production /tmp/docker
done
```

##Push images

```sh
buildah login registry.gitlab.com
buildah manifest push registry.gitlab.com/xhibitsignage-v3/xhibitsignage-v3-backend:php-8.1.21 docker://registry.gitlab.com/xhibitsignage-v3/xhibitsignage-v3-backend:php-8.1.21 --all --format v2s2
```
