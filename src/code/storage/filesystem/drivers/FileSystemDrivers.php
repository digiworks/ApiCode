<?php



namespace code\storage\filesystem\drivers;


class FileSystemDrivers {
    const LocalFS = LocalFS::class;
    const AwsS3FS = AwsS3V3::class;
    const AsyncAwsS3FS = AsyncAwsS3::class;
}
